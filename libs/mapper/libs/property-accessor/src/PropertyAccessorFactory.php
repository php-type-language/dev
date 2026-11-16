<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\Mapper\PropertyAccessor\PropertyReader\DirectPropertyReader;
use TypeLang\Mapper\PropertyAccessor\PropertyReader\RawPropertyReader;
use TypeLang\Mapper\PropertyAccessor\PropertyReader\ReflectionPropertyReader;
use TypeLang\Mapper\PropertyAccessor\PropertyWriter\DirectPropertyWriter;
use TypeLang\Mapper\PropertyAccessor\PropertyWriter\RawPropertyWriter;
use TypeLang\Mapper\PropertyAccessor\PropertyWriter\ReflectionPropertyWriter;

/**
 * Reads and writes a property bypassing its visibility. A property with a
 * backing store and a hook is accessed bypassing the hook, the hooks of a
 * virtual property are called (PHP 8.4+).
 */
final class PropertyAccessorFactory implements PropertyAccessorFactoryInterface
{
    /**
     * Returns the reader of the property:
     *
     * - bypassing the get hook, for a property with a backing store and the
     *   hook
     * - directly, for a public property, a virtual one is read through its
     *   get hook
     * - through the reflection, for any other property
     *
     * @throws PropertyNotFoundException in case of the property is static
     * @throws PropertyNotReadableException in case of the property is
     *         virtual without a get hook
     */
    public function createReader(\ReflectionProperty $property): PropertyReaderInterface
    {
        if ($property->isStatic()) {
            throw PropertyNotFoundException::becausePropertyIsStatic($property->class, $property->name);
        }

        $isVirtual = self::isVirtual($property);
        $hasHook = self::hasGetHook($property);

        if ($isVirtual && !$hasHook) {
            throw PropertyNotReadableException::becausePropertyIsWriteOnly($property->class, $property->name);
        }

        if (!$isVirtual && $hasHook) {
            return new RawPropertyReader($property);
        }

        if ($property->isPublic()) {
            /** @var non-empty-string */
            $name = $property->name;

            return new DirectPropertyReader($name);
        }

        return new ReflectionPropertyReader($property);
    }

    /**
     * Returns the writer of the property:
     *
     * - bypassing the set hook, for a property with a backing store and the
     *   hook
     * - directly, for a property writable outside the class
     * - through the reflection, for any other property, a virtual one is
     *   written through its set hook
     *
     * @throws PropertyNotFoundException in case of the property is static
     * @throws PropertyNotWritableException in case of the property is
     *         virtual without a set hook
     */
    public function createWriter(\ReflectionProperty $property): PropertyWriterInterface
    {
        if ($property->isStatic()) {
            throw PropertyNotFoundException::becausePropertyIsStatic($property->class, $property->name);
        }

        $isVirtual = self::isVirtual($property);
        $hasHook = self::hasSetHook($property);

        if ($isVirtual && !$hasHook) {
            throw PropertyNotWritableException::becausePropertyIsReadOnly($property->class, $property->name);
        }

        if (!$isVirtual && $hasHook) {
            return new RawPropertyWriter($property);
        }

        if (!$hasHook && self::isPublicSet($property)) {
            /** @var non-empty-string */
            $name = $property->name;

            return new DirectPropertyWriter($name);
        }

        return new ReflectionPropertyWriter($property);
    }

    /**
     * Returns {@see true} in case of a value can be assigned to the
     * property outside the class.
     */
    private static function isPublicSet(\ReflectionProperty $property): bool
    {
        if (!$property->isPublic() || $property->isReadOnly()) {
            return false;
        }

        if (\PHP_VERSION_ID < 80400) {
            return true;
        }

        // @phpstan-ignore-next-line PHP 8.4 API, phpVersion.min is 8.1
        return !$property->isPrivateSet() && !$property->isProtectedSet();
    }

    private static function isVirtual(\ReflectionProperty $property): bool
    {
        if (\PHP_VERSION_ID < 80400) {
            return false;
        }

        // @phpstan-ignore-next-line PHP 8.4 API, phpVersion.min is 8.1
        return $property->isVirtual();
    }

    private static function hasGetHook(\ReflectionProperty $property): bool
    {
        if (\PHP_VERSION_ID < 80400) {
            return false;
        }

        // @phpstan-ignore-next-line PHP 8.4 API, phpVersion.min is 8.1
        return $property->hasHook(\PropertyHookType::Get);
    }

    private static function hasSetHook(\ReflectionProperty $property): bool
    {
        if (\PHP_VERSION_ID < 80400) {
            return false;
        }

        // @phpstan-ignore-next-line PHP 8.4 API, phpVersion.min is 8.1
        return $property->hasHook(\PropertyHookType::Set);
    }
}
