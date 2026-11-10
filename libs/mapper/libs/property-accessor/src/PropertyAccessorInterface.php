<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAccessorExceptionInterface;

/**
 * Reads and writes properties of the object the accessor is bound to.
 *
 * @template T of object
 */
interface PropertyAccessorInterface
{
    /**
     * Returns the value of the specified property.
     *
     * @throws PropertyAccessorExceptionInterface<T> in case of the value
     *         cannot be read
     */
    public function read(string $property): mixed;

    /**
     * Returns {@see true} if the property can be read, and {@see false}
     * otherwise. Never throws.
     */
    public function isReadable(string $property): bool;

    /**
     * Updates the value of the specified property.
     *
     * @throws PropertyAccessorExceptionInterface<T> in case of the value
     *         cannot be written
     */
    public function write(string $property, mixed $value): void;

    /**
     * Returns {@see true} if the property can be written, and {@see false}
     * otherwise. Never throws.
     */
    public function isWritable(string $property): bool;
}
