<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\InvalidPropertyValueException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAccessException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAlreadyInitializedException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\Mapper\PropertyAccessor\Exception\UninitializedPropertyException;

/**
 * Reads and writes properties of any visibility using the PHP 8.1 - 8.3
 * reflection API.
 *
 * Property hooks of PHP 8.4+ are not taken into account, see
 * {@see ModernReflectionPropertyAccessor}.
 *
 * @template T of object
 *
 * @template-extends PropertyAccessor<T>
 */
final class ReflectionPropertyAccessor extends PropertyAccessor
{
    /**
     * @var array<string, \ReflectionProperty>
     */
    private readonly array $properties;

    /**
     * @param \ReflectionClass<T> $class
     */
    public function __construct(\ReflectionClass $class)
    {
        $properties = [];

        foreach ($class->getProperties() as $property) {
            if (!$property->isStatic()) {
                $properties[$property->name] = $property;
            }
        }

        $this->properties = $properties;
    }

    public function read(string $property): mixed
    {
        $reflection = $this->properties[$property]
            ?? throw $this->createReadException($property);

        try {
            return $reflection->getValue($this->object);
        } catch (\Throwable $e) {
            throw $this->createReadException($property, $e);
        }
    }

    public function isReadable(string $property): bool
    {
        $reflection = $this->properties[$property] ?? null;

        try {
            return $reflection !== null
                && $reflection->isInitialized($this->object);
        } catch (\Throwable) {
            return false;
        }
    }

    public function write(string $property, mixed $value): void
    {
        $reflection = $this->properties[$property]
            ?? throw $this->createWriteException($property, $value);

        try {
            $reflection->setValue($this->object, $value);
        } catch (\Throwable $e) {
            throw $this->createWriteException($property, $value, $e);
        }
    }

    public function isWritable(string $property): bool
    {
        $reflection = $this->properties[$property] ?? null;

        if ($reflection === null) {
            return false;
        }

        try {
            return !$reflection->isReadOnly()
                || !$reflection->isInitialized($this->object);
        } catch (\Throwable) {
            return false;
        }
    }

    private function createReadException(string $property, ?\Throwable $previous = null): PropertyAccessException
    {
        try {
            $reflection = new \ReflectionProperty($this->object, $property);
        } catch (\ReflectionException $e) {
            return PropertyNotFoundException::becausePropertyDoesNotExist($this->object, $property, $e);
        }

        if ($reflection->isStatic()) {
            return PropertyNotFoundException::becausePropertyIsStatic($this->object, $property);
        }

        if (!$reflection->isInitialized($this->object)) {
            return UninitializedPropertyException::becausePropertyIsNotInitialized($this->object, $property);
        }

        return PropertyNotReadableException::becauseReadingFailed($this->object, $property, $previous);
    }

    private function createWriteException(
        string $property,
        mixed $value,
        ?\Throwable $previous = null,
    ): PropertyAccessException {
        try {
            $reflection = new \ReflectionProperty($this->object, $property);
        } catch (\ReflectionException $e) {
            return PropertyNotFoundException::becausePropertyDoesNotExist($this->object, $property, $e);
        }

        if ($reflection->isStatic()) {
            return PropertyNotFoundException::becausePropertyIsStatic($this->object, $property);
        }

        if ($reflection->isReadOnly() && $reflection->isInitialized($this->object)) {
            return PropertyAlreadyInitializedException::becausePropertyIsAlreadyInitialized($this->object, $property);
        }

        if ($previous instanceof \TypeError) {
            return InvalidPropertyValueException::becauseValueTypeIsInvalid(
                object: $this->object,
                property: $property,
                expected: (string) $reflection->getType(),
                value: $value,
                previous: $previous,
            );
        }

        return PropertyNotWritableException::becauseWritingFailed($this->object, $property, $previous);
    }
}
