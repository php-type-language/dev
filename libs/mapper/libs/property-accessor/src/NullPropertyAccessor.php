<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor;

use TypeLang\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\PropertyAccessor\Exception\PropertyNotWritableException;

final readonly class NullPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @throws PropertyNotReadableException
     */
    public function getValue(object $object, string $property): mixed
    {
        throw PropertyNotReadableException::becausePropertyIsNotReadable($object, $property);
    }

    public function isReadable(object $object, string $property): bool
    {
        return false;
    }

    /**
     * @throws PropertyNotWritableException
     */
    public function setValue(object $object, string $property, mixed $value): void
    {
        throw PropertyNotWritableException::becausePropertyIsNotWritable($object, $property);
    }

    public function isWritable(object $object, string $property): bool
    {
        return false;
    }
}
