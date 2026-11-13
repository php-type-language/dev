<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\InvalidDefaultValueException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\DefaultValueFactory;
use TypeLang\Mapper\Mapping\Reader\Internal\ErrorCollector;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Mapper\Mapping\Reader\Reflection\PropertySelectorInterface;
use TypeLang\Mapper\Mapping\Reader\Reflection\PublicPropertySelector;
use TypeLang\Reader\Exception\ReaderExceptionInterface;
use TypeLang\Reader\PropertyAccessDirection;
use TypeLang\Reader\ReflectionReader;

/**
 * Base of the chain: adds the properties chosen by the selector with their
 * native types and default values, the default value of the property or of
 * the promoted constructor parameter. Over the given metadata it overrides
 * these values of the chosen properties, adds the chosen properties that
 * are missing and keeps the other ones. Other readers override the values
 * of the added properties only.
 */
final class ReflectionMetadataReader implements MetadataReaderInterface
{
    private readonly ReflectionReader $nativeTypeReader;

    private readonly SourceContextFactory $contexts;

    public function __construct(
        private readonly PropertySelectorInterface $selector = new PublicPropertySelector(),
    ) {
        $this->nativeTypeReader = new ReflectionReader();
        $this->contexts = new SourceContextFactory();
    }

    /**
     * @throws InvalidMetadataException in case of a default value cannot be
     *         evaluated or is an object other than an enum case
     */
    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());
        $errors = new ErrorCollector();

        foreach ($class->getProperties() as $reflection) {
            $name = $reflection->name;

            if ($name === '' || !$this->selector->isSelected($reflection)) {
                continue;
            }

            $property = $metadata->findProperty($name) ?? new PropertyMetadata($name);
            $property = $this->readProperty($class, $reflection, $property, $errors);

            $metadata = $metadata->withProperty($property);
        }

        $errors->throwIfNotEmpty($metadata);

        return $metadata;
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function readProperty(
        \ReflectionClass $class,
        \ReflectionProperty $reflection,
        PropertyMetadata $property,
        ErrorCollector $errors,
    ): PropertyMetadata {
        $context = $this->contexts->createFromReflection($class, $reflection);
        $read = $this->findType($reflection, PropertyAccessDirection::Read, $context);
        $write = $this->findType($reflection, PropertyAccessDirection::Write, $context);

        if ($read !== null) {
            $property = $property->withReadType($read);
        }

        if ($write !== null) {
            $property = $property->withWriteType($write);
        }

        try {
            $default = DefaultValueFactory::findForProperty($reflection, $context);
        } catch (InvalidDefaultValueException $e) {
            $errors->addError($e);

            return $property;
        }

        if ($default !== null) {
            $property = $property->withDefaultValue($default);
        }

        return $property;
    }

    private function findType(
        \ReflectionProperty $reflection,
        PropertyAccessDirection $direction,
        SourceContext $context,
    ): ?TypeMetadata {
        try {
            $type = $this->nativeTypeReader->findPropertyType($reflection, $direction);
        } catch (ReaderExceptionInterface $e) {
            return $context->createInvalidType($e);
        }

        if ($type === null) {
            return null;
        }

        return TypeMetadata::createFromType($type, $context->source);
    }
}
