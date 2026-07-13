<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor;

use TypeLang\PropertyAccessor\Exception\MissingPropertyReadException;
use TypeLang\PropertyAccessor\Exception\MissingPropertyWriteException;
use TypeLang\PropertyAccessor\Exception\PropertyAlreadyInitializedException;
use TypeLang\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\PropertyAccessor\Exception\ReadOnlyPropertyException;
use TypeLang\PropertyAccessor\Exception\StaticPropertyReadException;
use TypeLang\PropertyAccessor\Exception\StaticPropertyWriteException;
use TypeLang\PropertyAccessor\Exception\UninitializedPropertyReadException;
use TypeLang\PropertyAccessor\Exception\WriteOnlyPropertyException;

final readonly class ReflectionPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @throws PropertyNotReadableException
     */
    public function getValue(object $object, string $property): mixed
    {
        try {
            $reflection = new \ReflectionProperty($object, $property);
        } catch (\ReflectionException $e) {
            throw MissingPropertyReadException::becausePropertyDoesNotExist($object, $property, $e);
        }

        if ($reflection->isStatic()) {
            throw StaticPropertyReadException::becausePropertyIsStatic($object, $property);
        }

        $isVirtual = $reflection->isVirtual();

        if ($isVirtual) {
            // A virtual property has no backing store: it can only be read
            // through its get hook.
            if ($reflection->getHook(\PropertyHookType::Get) === null) {
                throw WriteOnlyPropertyException::becausePropertyIsWriteOnly($object, $property);
            }
        } elseif (!$reflection->isInitialized($object)) {
            // A non-virtual property is read straight from the backing store,
            // which must therefore be initialized.
            throw UninitializedPropertyReadException::becausePropertyIsNotInitialized($object, $property);
        }

        try {
            // Non-virtual properties are read from the backing store, bypassing
            // any get hook; virtual ones can only be read through the hook.
            return $isVirtual
                ? $reflection->getValue($object)
                : $reflection->getRawValue($object);
        } catch (\Throwable $e) {
            throw PropertyNotReadableException::becausePropertyIsNotReadable($object, $property, $e);
        }
    }

    public function isReadable(object $object, string $property): bool
    {
        try {
            $reflection = new \ReflectionProperty($object, $property);
        } catch (\ReflectionException) {
            return false;
        }

        return match (true) {
            $reflection->isStatic() => false,
            $reflection->isVirtual() => $reflection->getHook(\PropertyHookType::Get) !== null,
            default => $reflection->isInitialized($object),
        };
    }

    /**
     * @throws PropertyNotWritableException
     */
    public function setValue(object $object, string $property, mixed $value): void
    {
        try {
            $reflection = new \ReflectionProperty($object, $property);
        } catch (\ReflectionException $e) {
            throw MissingPropertyWriteException::becausePropertyDoesNotExist($object, $property, $e);
        }

        if ($reflection->isStatic()) {
            throw StaticPropertyWriteException::becausePropertyIsStatic($object, $property);
        }

        // A readonly property may be initialized only once.
        if ($reflection->isReadOnly() && $reflection->isInitialized($object)) {
            throw PropertyAlreadyInitializedException::becausePropertyIsAlreadyInitialized($object, $property);
        }

        $isVirtual = $reflection->isVirtual();

        // A virtual property has no backing store: it can only be written
        // through its set hook.
        if ($isVirtual && $reflection->getHook(\PropertyHookType::Set) === null) {
            throw ReadOnlyPropertyException::becausePropertyIsReadOnly($object, $property);
        }

        try {
            if ($isVirtual) {
                $reflection->setValue($object, $value);

                return;
            }

            // A non-virtual property is written straight to the backing store,
            // bypassing any set hook and without triggering lazy-initialization
            // of a ghost/proxy object.
            $reflection->setRawValueWithoutLazyInitialization($object, $value);
        } catch (\Throwable $e) {
            throw PropertyNotWritableException::becausePropertyIsNotWritable($object, $property, $e);
        }
    }

    public function isWritable(object $object, string $property): bool
    {
        try {
            $reflection = new \ReflectionProperty($object, $property);
        } catch (\ReflectionException) {
            return false;
        }

        return match (true) {
            $reflection->isStatic() => false,
            $reflection->isVirtual() => $reflection->getHook(\PropertyHookType::Set) !== null,
            $reflection->isReadOnly() => !$reflection->isInitialized($object),
            default => true,
        };
    }
}
