<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Type\Builder;

use Symfony\Component\ExpressionLanguage\SyntaxError;
use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\DiscriminatorMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\ExpressionSkipCondition;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Type\ClassType;
use TypeLang\Mapper\Type\ClassType\Property;
use TypeLang\Mapper\Type\ClassType\TagCheck;
use TypeLang\Mapper\Type\Extractor\DiscriminatorTagExtractor;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;

/**
 * Creates the type of a user class by its metadata. Internal classes, such
 * as {@see \DateTimeImmutable} or {@see \stdClass}, are left to their own
 * types.
 *
 * The metadata decide which properties take part in each direction. The
 * built type is checked against the strategies of the mapper: a class whose
 * objects cannot be created, such as an abstract one or an enum, or a
 * property that cannot be accessed in its direction is an error of the type.
 *
 * @template-implements TypeBuilderInterface<object>
 */
final class ClassTypeBuilder implements TypeBuilderInterface
{
    /**
     * @return ClassType<object>|null
     * @throws TypeDefinitionException in case of a property has no type or
     *         the class cannot be created or accessed
     */
    public function tryBuild(TypeNode $node, BuildContext $context): ?ClassType
    {
        if (!$node instanceof NamedTypeNode || $node->arguments !== null || $node->fields !== null) {
            return null;
        }

        $name = $node->name->toClassString();

        if ($name === null || !\class_exists($name)) {
            return null;
        }

        $class = new \ReflectionClass($name);

        if ($class->isInternal()) {
            return null;
        }

        $metadata = $context->metadata->read($class);
        $writable = $readable = [];

        foreach ($metadata->properties as $property) {
            self::checkExpressions($class, $property, $context);

            if ($property->write !== null) {
                $writable[] = self::createProperty($class, $property, $property->write->type, $context);
            }

            if ($property->read !== null) {
                $readable[] = self::createProperty($class, $property, $property->read->type, $context);
            }
        }

        $context->accessors->get($metadata);

        $fields = $tags = $owners = $checks = [];

        // tags of the class in the maps of its bases and of its own one,
        // the root ones first
        foreach ([...self::getBases($class, $context), $metadata] as $base) {
            $discriminator = $base->discriminator;

            if ($discriminator === null) {
                continue;
            }

            $field = $discriminator->field;
            $fields[] = $field;
            $checks[$field] ??= self::createTagCheck($class, $discriminator, $writable);

            $tag = DiscriminatorTagExtractor::extract(
                DiscriminatorTagExtractor::extractIndex($discriminator),
                $class->name,
            );

            if ($tag === null) {
                continue;
            }

            if (\array_key_exists($field, $tags) && $tags[$field] !== $tag) {
                throw TypeDefinitionException::becauseDiscriminatorFieldIsAmbiguous(
                    $class->name,
                    $field,
                    $owners[$field],
                    $tags[$field],
                    $base->name,
                    $tag,
                );
            }

            $tags[$field] = $tag;
            $owners[$field] ??= $base->name;
        }

        $mapped = self::findMappedParent($class, [...self::getBases($class, $context), $metadata]);

        if ($mapped !== null) {
            $readable = self::filterReadable($readable, $context->metadata->read(new \ReflectionClass($mapped)));
        }

        return new ClassType(
            metadata: $metadata,
            writable: $writable,
            readable: $readable,
            tagFields: $fields,
            tags: $tags,
            tagChecks: \array_values(\array_filter($checks)),
        );
    }

    /**
     * Returns the nearest parent of the class in the maps of the bases, the
     * class is normalized as it: {@see null} in case of the class itself is
     * in a map or none of its parents is.
     *
     * @param \ReflectionClass<object> $class
     * @param list<ClassMetadata<object>> $bases
     * @return class-string|null
     */
    private static function findMappedParent(\ReflectionClass $class, array $bases): ?string
    {
        $nearest = null;

        foreach ($bases as $base) {
            if ($base->discriminator === null) {
                continue;
            }

            $index = DiscriminatorTagExtractor::extractIndex($base->discriminator);

            if (isset($index[$class->name])) {
                return null;
            }

            foreach ($index as $mapped => $tag) {
                if ($class->isSubclassOf($mapped) && ($nearest === null || \is_subclass_of($mapped, $nearest))) {
                    $nearest = $mapped;
                }
            }
        }

        return $nearest;
    }

    /**
     * Returns the properties taken on normalization that the parent takes
     * too.
     *
     * @param list<Property<mixed>> $readable
     * @param ClassMetadata<object> $parent
     * @return list<Property<mixed>>
     */
    private static function filterReadable(array $readable, ClassMetadata $parent): array
    {
        $result = [];

        foreach ($readable as $property) {
            if (($parent->properties[$property->metadata->name] ?? null)?->read !== null) {
                $result[] = $property;
            }
        }

        return $result;
    }

    /**
     * Returns the tags of the class the data may have in the field of the
     * discriminator, {@see null} in case of the field is not checked: the
     * map has no tag of the class and it does not take the data with an
     * unknown tag, or a property of the class takes the field.
     *
     * @param \ReflectionClass<object> $class
     * @param list<Property<mixed>> $writable
     */
    private static function createTagCheck(
        \ReflectionClass $class,
        DiscriminatorMetadata $discriminator,
        array $writable,
    ): ?TagCheck {
        $field = $discriminator->field;

        foreach ($writable as $property) {
            if ($property->metadata->externalName === $field || \in_array($field, $property->metadata->aliases, true)) {
                return null;
            }
        }

        $index = DiscriminatorTagExtractor::extractIndex($discriminator);
        $nearest = DiscriminatorTagExtractor::extract($index, $class->name);
        $isDefault = $discriminator->default !== null
            && DiscriminatorTagExtractor::extractClass($discriminator->default) === $class->name;

        if ($nearest === null && !$isDefault) {
            return null;
        }

        $owner = \array_search($nearest, $index, true);
        $tags = $known = [];

        foreach ($discriminator->map as $tag => $type) {
            if ($owner !== false && DiscriminatorTagExtractor::extractClass($type) === $owner) {
                $tags[$tag] = true;
            } else {
                $known[$tag] = true;
            }
        }

        if ($isDefault) {
            return new TagCheck($field, $tags, $known);
        }

        return new TagCheck($field, $tags);
    }

    /**
     * Returns the metadata of the interfaces and the parents of the class
     * with a discriminator, the root ones first.
     *
     * @param \ReflectionClass<object> $class
     * @return list<ClassMetadata<object>>
     */
    private static function getBases(\ReflectionClass $class, BuildContext $context): array
    {
        $bases = [];

        foreach ([...\class_implements($class->name), ...\array_reverse(\class_parents($class->name))] as $name) {
            $reflection = new \ReflectionClass($name);

            if ($reflection->isInternal()) {
                continue;
            }

            $metadata = $context->metadata->read($reflection);

            if ($metadata->discriminator !== null) {
                $bases[] = $metadata;
            }
        }

        return $bases;
    }

    /**
     * @param \ReflectionClass<object> $class
     * @param TypeMetadata|null $type type of the property in the direction
     * @return Property<mixed>
     * @throws TypeDefinitionException in case of the property has no type or
     *         its source cannot read it
     */
    private static function createProperty(
        \ReflectionClass $class,
        PropertyMetadata $property,
        ?TypeMetadata $type,
        BuildContext $context,
    ): Property {
        if ($type === null) {
            throw TypeDefinitionException::becausePropertyTypeIsNotDefined($class->name, $property->name);
        }

        if ($type->type === null) {
            \assert($type->error !== null);

            throw TypeDefinitionException::becausePropertyTypeCannotBeRead($class->name, $property->name, $type->error);
        }

        return new Property($property, $context->types->getType($type->type), $type->type);
    }

    /**
     * Checks the skip expressions of the property against the language of
     * the mapper, such as an expression read by another language or taken
     * from a cache with a function the language does not have.
     *
     * @param \ReflectionClass<object> $class
     * @throws TypeDefinitionException in case of an expression cannot be
     *         parsed by the language
     */
    private static function checkExpressions(
        \ReflectionClass $class,
        PropertyMetadata $property,
        BuildContext $context,
    ): void {
        $expressions = $context->expressions;

        if ($expressions === null) {
            return;
        }

        foreach ($property->skip as $condition) {
            if (!$condition instanceof ExpressionSkipCondition) {
                continue;
            }

            try {
                $expressions->parse(
                    (string) $condition->expression,
                    [$condition->variable, ExpressionSkipCondition::VALUE],
                );
            } catch (SyntaxError $e) {
                throw TypeDefinitionException::becauseSkipExpressionIsInvalid(
                    $class->name,
                    $property->name,
                    (string) $condition->expression,
                    $e,
                );
            }
        }
    }
}
