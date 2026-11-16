<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TypeLang\Mapper\Mapping\DiscriminatorMap;
use TypeLang\Mapper\Mapping\Exception\InvalidAttributeException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\Exception\UnknownPropertyException;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\MapType;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\EmptySkipCondition;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\NullSkipCondition;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\SkipConditionInterface;
use TypeLang\Mapper\Mapping\Metadata\SourceInfo;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Mapping\NormalizeAsArray;
use TypeLang\Mapper\Mapping\OnTypeError;
use TypeLang\Mapper\Mapping\OnUndefinedError;
use TypeLang\Mapper\Mapping\Reader\Internal\DiscriminatorFactory;
use TypeLang\Mapper\Mapping\Reader\Internal\ErrorCollector;
use TypeLang\Mapper\Mapping\Reader\Internal\PropertyHooks;
use TypeLang\Mapper\Mapping\Reader\Internal\SkipConditionFactory;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Mapper\Mapping\SkipWhen;
use TypeLang\Mapper\Mapping\SkipWhenEmpty;
use TypeLang\Mapper\Mapping\SkipWhenNull;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Overrides values of the given properties with {@see MapName} and
 * {@see MapType}, an attribute of a property that is not given is an error.
 * {@see MapType} of the property sets both types, of its `get` hook the read
 * type, of its `set` hook the write type, of any other method is an error.
 * {@see SkipWhenNull}, {@see SkipWhenEmpty} and {@see SkipWhen} of the
 * property set its skip conditions, an expression is parsed here.
 * {@see OnTypeError} and {@see OnUndefinedError} set the messages of the
 * property, {@see OnTypeError} of the class the message of the class.
 * {@see DiscriminatorMap} of the class sets its polymorphism,
 * {@see NormalizeAsArray} the form of its normalized objects. Names in the
 * types are resolved by the imports of the file declaring the property or
 * the class.
 */
final class AttributeMetadataReader implements MetadataReaderInterface
{
    private readonly SourceContextFactory $contexts;

    /**
     * @param TypeParserInterface<TypeNode, string> $parser parser of the
     *        types of {@see MapType}
     */
    private readonly SkipConditionFactory $conditions;

    /**
     * @param ExpressionLanguage|null $expressions parser of the expressions of
     *        {@see SkipWhen}, created on the first one in case of {@see null}
     */
    public function __construct(
        private readonly TypeParserInterface $parser = new TypeParser(),
        ?ExpressionLanguage $expressions = null,
    ) {
        $this->contexts = new SourceContextFactory();
        $this->conditions = new SkipConditionFactory($expressions);
    }

    /**
     * @throws InvalidMetadataException in case of an attribute is wrong,
     *         belongs to a property that is not given or to a method
     * @throws MissingDependencyException in case of {@see SkipWhen} is
     *         given, but the `symfony/expression-language` package is not
     *         installed
     */
    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());
        $errors = new ErrorCollector();

        foreach ($class->getProperties() as $property) {
            $metadata = $this->readProperty($class, $property, $metadata, $errors);
        }

        foreach ($class->getMethods() as $method) {
            self::checkMethod($class, $method, $errors);
        }

        $metadata = $this->readDiscriminator($class, $metadata, $errors);
        $metadata = $this->readClassOptions($class, $metadata, $errors);

        $errors->throwIfNotEmpty($metadata);

        return $metadata;
    }

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @param ClassMetadata<T> $metadata
     * @return ClassMetadata<T>
     */
    private function readDiscriminator(
        \ReflectionClass $class,
        ClassMetadata $metadata,
        ErrorCollector $errors,
    ): ClassMetadata {
        $attributes = $class->getAttributes(DiscriminatorMap::class);

        if ($attributes === []) {
            return $metadata;
        }

        $context = $this->contexts->createFromClass($class);

        foreach ($attributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);

            if (!$instance instanceof DiscriminatorMap) {
                continue;
            }

            $discriminator = DiscriminatorFactory::create(
                field: $instance->field,
                map: $instance->map,
                otherwise: $instance->otherwise,
                context: $context,
                parser: $this->parser,
                errors: $errors,
            );

            if ($discriminator !== null) {
                $metadata = $metadata->withDiscriminator($discriminator);
            }
        }

        return $metadata;
    }

    /**
     * Reads {@see OnTypeError} and {@see NormalizeAsArray} of the class.
     *
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @param ClassMetadata<T> $metadata
     * @return ClassMetadata<T>
     */
    private function readClassOptions(
        \ReflectionClass $class,
        ClassMetadata $metadata,
        ErrorCollector $errors,
    ): ClassMetadata {
        $attributes = [
            ...$class->getAttributes(OnTypeError::class),
            ...$class->getAttributes(NormalizeAsArray::class),
        ];

        if ($attributes === []) {
            return $metadata;
        }

        $context = $this->contexts->createFromClass($class);

        foreach ($attributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);
            $message = self::findMessage($instance, $context, $errors);

            if ($message !== null) {
                $metadata = $metadata->withTypeErrorMessage($message);
            }

            if ($instance instanceof NormalizeAsArray) {
                $metadata = $metadata->withNormalizeAsArray($instance->enabled);
            }
        }

        return $metadata;
    }

    /**
     * Adds an error for each {@see MapType} of a method that is not a
     * property hook.
     *
     * @param \ReflectionClass<object> $class
     */
    private static function checkMethod(
        \ReflectionClass $class,
        \ReflectionMethod $method,
        ErrorCollector $errors,
    ): void {
        $attributes = $method->getAttributes(MapType::class);

        if ($attributes === []) {
            return;
        }

        $source = SourceInfo::createFromReflection($method);

        foreach ($attributes as $attribute) {
            $errors->addError(InvalidAttributeException::becauseAttributeIsOnMethod(
                attribute: $attribute->getName(),
                method: $class->name . '::' . $method->name . '()',
                source: $source,
            ));
        }
    }

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @param ClassMetadata<T> $metadata
     * @return ClassMetadata<T>
     */
    private function readProperty(
        \ReflectionClass $class,
        \ReflectionProperty $reflection,
        ClassMetadata $metadata,
        ErrorCollector $errors,
    ): ClassMetadata {
        $name = $reflection->name;
        $nameAttributes = $reflection->getAttributes(MapName::class);
        $typeAttributes = $reflection->getAttributes(MapType::class);
        $getAttributes = self::getHookAttributes(PropertyHooks::findGet($reflection));
        $setAttributes = self::getHookAttributes(PropertyHooks::findSet($reflection));
        $skipAttributes = [
            ...$reflection->getAttributes(SkipWhenNull::class),
            ...$reflection->getAttributes(SkipWhenEmpty::class),
            ...$reflection->getAttributes(SkipWhen::class),
        ];
        $messageAttributes = [
            ...$reflection->getAttributes(OnTypeError::class),
            ...$reflection->getAttributes(OnUndefinedError::class),
        ];

        $attributes = [
            ...$nameAttributes,
            ...$typeAttributes,
            ...$getAttributes,
            ...$setAttributes,
            ...$skipAttributes,
            ...$messageAttributes,
        ];

        if ($name === '' || $attributes === []) {
            return $metadata;
        }

        $context = $this->contexts->createFromReflection($class, $reflection);

        $property = $metadata->findProperty($name);

        if ($property === null) {
            $errors->addError(UnknownPropertyException::becausePropertyIsNotMapped(
                $context->target,
                $context->source,
            ));

            return $metadata;
        }

        foreach ($nameAttributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);

            if ($instance instanceof MapName) {
                $property = self::loadName($instance, $property, $context, $errors);
            }
        }

        $type = $this->findType($typeAttributes, $context, $errors);

        if ($type !== null) {
            $property = $property->withType($type);
        }

        $read = $this->findType($getAttributes, $context, $errors);

        if ($read !== null) {
            $property = $property->withReadType($read);
        }

        $write = $this->findType($setAttributes, $context, $errors);

        if ($write !== null) {
            $property = $property->withWriteType($write);
        }

        if ($skipAttributes !== []) {
            $property = $property->withSkipConditions($this->createConditions($skipAttributes, $context, $errors));
        }

        foreach ($messageAttributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);
            $message = self::findMessage($instance, $context, $errors);

            if ($message !== null && $instance instanceof OnTypeError) {
                $property = $property->withTypeErrorMessage($message);
            }

            if ($message !== null && $instance instanceof OnUndefinedError) {
                $property = $property->withUndefinedErrorMessage($message);
            }
        }

        return $metadata->withProperty($property);
    }

    /**
     * Returns the message of {@see OnTypeError} or {@see OnUndefinedError},
     * {@see null} in case of the attribute cannot be created or its message
     * is empty, the error is added.
     *
     * @return non-empty-string|null
     */
    private static function findMessage(?object $attribute, SourceContext $context, ErrorCollector $errors): ?string
    {
        if (!$attribute instanceof OnTypeError && !$attribute instanceof OnUndefinedError) {
            return null;
        }

        if ($attribute->message === '') {
            $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyString(
                'message',
                $context->target,
                $context->source,
            ));

            return null;
        }

        return $attribute->message;
    }

    /**
     * Returns the conditions of the attributes that can be created.
     *
     * @param list<\ReflectionAttribute<object>> $attributes
     * @return list<SkipConditionInterface>
     * @throws MissingDependencyException in case of an expression is given,
     *         but the `symfony/expression-language` package is not installed
     */
    private function createConditions(array $attributes, SourceContext $context, ErrorCollector $errors): array
    {
        $conditions = [];

        foreach ($attributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);

            if ($instance instanceof SkipWhenNull) {
                $conditions[] = new NullSkipCondition();
            }

            if ($instance instanceof SkipWhenEmpty) {
                $conditions[] = new EmptySkipCondition();
            }

            if (!$instance instanceof SkipWhen) {
                continue;
            }

            try {
                $conditions[] = $this->conditions->createExpression($instance->expr, $instance->context, $context);
            } catch (InvalidValueException $e) {
                $errors->addError($e);
            }
        }

        return $conditions;
    }

    /**
     * Returns the type of the last {@see MapType} of the list, {@see null} in
     * case of there is none or it cannot be created.
     *
     * @param list<\ReflectionAttribute<MapType>> $attributes
     */
    private function findType(array $attributes, SourceContext $context, ErrorCollector $errors): ?TypeMetadata
    {
        $type = null;

        foreach ($attributes as $attribute) {
            $instance = self::createAttribute($attribute, $context, $errors);

            if ($instance instanceof MapType) {
                $type = $this->createType($instance, $context);
            }
        }

        return $type;
    }

    /**
     * Returns {@see MapType} attributes of the hook, an empty list in case
     * of there is no hook.
     *
     * @return list<\ReflectionAttribute<MapType>>
     */
    private static function getHookAttributes(?\ReflectionMethod $hook): array
    {
        return $hook?->getAttributes(MapType::class) ?? [];
    }

    private static function loadName(
        MapName $attribute,
        PropertyMetadata $property,
        SourceContext $context,
        ErrorCollector $errors,
    ): PropertyMetadata {
        if ($attribute->name === '') {
            $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyString(
                'name',
                $context->target,
                $context->source,
            ));

            return $property;
        }

        return $property->withExternalName($attribute->name);
    }

    private function createType(MapType $attribute, SourceContext $context): TypeMetadata
    {
        $type = $context->parseType($this->parser, $attribute->type);

        if ($attribute->strict === null) {
            return $type;
        }

        return $type->withStrict($attribute->strict);
    }

    /**
     * @param \ReflectionAttribute<object> $attribute
     */
    private static function createAttribute(
        \ReflectionAttribute $attribute,
        SourceContext $context,
        ErrorCollector $errors,
    ): ?object {
        try {
            return $attribute->newInstance();
        } catch (\Throwable $e) {
            $errors->addError(InvalidAttributeException::becauseAttributeCannotBeCreated(
                attribute: $attribute->getName(),
                property: $context->target,
                source: $context->source,
                previous: $e,
            ));
        }

        return null;
    }
}
