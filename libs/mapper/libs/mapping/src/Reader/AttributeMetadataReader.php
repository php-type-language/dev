<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\InvalidAttributeException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\UnknownPropertyException;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\MapType;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\SourceInfo;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\ErrorCollector;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Overrides values of the given properties with {@see MapName} and
 * {@see MapType}, an attribute of a property that is not given is an error.
 * {@see MapType} of the property sets both types, of its `get` hook the read
 * type, of its `set` hook the write type, of any other method is an error.
 * Names in the types are resolved by the imports of the file declaring the
 * property.
 */
final class AttributeMetadataReader implements MetadataReaderInterface
{
    private readonly SourceContextFactory $contexts;

    /**
     * @param TypeParserInterface<TypeNode, string> $parser parser of the
     *        types of {@see MapType}
     */
    public function __construct(
        private readonly TypeParserInterface $parser = new TypeParser(),
    ) {
        $this->contexts = new SourceContextFactory();
    }

    /**
     * @throws InvalidMetadataException in case of an attribute is wrong,
     *         belongs to a property that is not given or to a method
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

        $errors->throwIfNotEmpty($metadata);

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
        $getAttributes = self::getGetHookAttributes($reflection);
        $setAttributes = self::getSetHookAttributes($reflection);

        if ($name === '' || [...$nameAttributes, ...$typeAttributes, ...$getAttributes, ...$setAttributes] === []) {
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

        return $metadata->withProperty($property);
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
     * Returns {@see MapType} attributes of the `get` hook of the property, an
     * empty list before PHP 8.4.
     *
     * @return list<\ReflectionAttribute<MapType>>
     */
    private static function getGetHookAttributes(\ReflectionProperty $property): array
    {
        if (\PHP_VERSION_ID < 80400) {
            return [];
        }

        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $hook = $property->getHook(\PropertyHookType::Get);

        return $hook?->getAttributes(MapType::class) ?? [];
    }

    /**
     * Returns {@see MapType} attributes of the `set` hook of the property, an
     * empty list before PHP 8.4.
     *
     * @return list<\ReflectionAttribute<MapType>>
     */
    private static function getSetHookAttributes(\ReflectionProperty $property): array
    {
        if (\PHP_VERSION_ID < 80400) {
            return [];
        }

        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $hook = $property->getHook(\PropertyHookType::Set);

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
