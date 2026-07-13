<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor;

use TypeLang\PropertyAccessor\Exception\PropertyAccessorExceptionInterface;

interface PropertyAccessorInterface
{
    /**
     * Returns the value of the specified field from the passed object.
     *
     * @throws PropertyAccessorExceptionInterface occurs in case of value cannot be reads
     */
    public function getValue(object $object, string $property): mixed;

    /**
     * Returns {@see true} if the field is readable by the implementation,
     * and {@see false} otherwise.
     *
     * This method MUST NOT return any exceptions.
     */
    public function isReadable(object $object, string $property): bool;

    /**
     * Updates the value of the specified field inside the passed object.
     *
     * @throws PropertyAccessorExceptionInterface occurs in case of value cannot be writes
     */
    public function setValue(object $object, string $property, mixed $value): void;

    /**
     * Returns {@see true} if the field is writable by the implementation,
     * and {@see false} otherwise.
     *
     * This method MUST NOT return any exceptions.
     */
    public function isWritable(object $object, string $property): bool;
}
