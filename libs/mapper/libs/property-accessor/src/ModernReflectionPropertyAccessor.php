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
 * Reads and writes properties of any visibility using the PHP 8.4+
 * reflection API.
 *
 * A property with a backing store is accessed bypassing its hooks,
 * a virtual property is accessed through its hooks.
 *
 * @template T of object
 *
 * @template-extends PropertyAccessor<T>
 */
final class ModernReflectionPropertyAccessor extends PropertyAccessor
{
    /**
     * @var array<string, \ReflectionProperty>
     */
    private readonly array $properties;

    /**
     * @var array<string, \ReflectionProperty>
     */
    private readonly array $getters;

    /**
     * @var array<string, \ReflectionProperty>
     */
    private readonly array $setters;

    /**
     * @param \ReflectionClass<T> $class
     * @throws \LogicException in case of PHP version is lower than 8.4
     */
    public function __construct(\ReflectionClass $class)
    {
        if (\PHP_VERSION_ID < 80400) {
            throw new \LogicException('This implementation requires PHP 8.4');
        }

        $properties = $getters = $setters = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
            if (!$property->isVirtual()) {
                $properties[$property->name] = $property;

                continue;
            }

            // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
            if ($property->hasHook(\PropertyHookType::Get)) {
                $getters[$property->name] = $property;
            }

            // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
            if ($property->hasHook(\PropertyHookType::Set)) {
                $setters[$property->name] = $property;
            }
        }

        $this->properties = $properties;
        $this->getters = $getters;
        $this->setters = $setters;
    }

    public function read(string $property): mixed
    {
        $reflection = $this->properties[$property] ?? null;

        if ($reflection !== null) {
            try {
                // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
                return $reflection->getRawValue($this->object);
            } catch (\Throwable $e) {
                throw $this->createReadException($property, $e);
            }
        }

        $reflection = $this->getters[$property]
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

        if ($reflection !== null) {
            try {
                return $reflection->isInitialized($this->object);
            } catch (\Throwable) {
                return false;
            }
        }

        return isset($this->getters[$property]);
    }

    public function write(string $property, mixed $value): void
    {
        $reflection = $this->properties[$property] ?? null;

        if ($reflection !== null) {
            try {
                // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
                $reflection->setRawValue($this->object, $value);
            } catch (\Throwable $e) {
                throw $this->createWriteException($property, $value, $e);
            }

            return;
        }

        $reflection = $this->setters[$property]
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
            return isset($this->setters[$property]);
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

        // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
        if ($reflection->isVirtual() && !$reflection->hasHook(\PropertyHookType::Get)) {
            return PropertyNotReadableException::becausePropertyIsWriteOnly($this->object, $property);
        }

        // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
        if (!$reflection->isVirtual() && !$reflection->isInitialized($this->object)) {
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

        // @phpstan-ignore-next-line PHP 8.4 API is unknown for phpVersion.min 8.1
        if ($reflection->isVirtual() && !$reflection->hasHook(\PropertyHookType::Set)) {
            return PropertyNotWritableException::becausePropertyIsReadOnly($this->object, $property);
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
