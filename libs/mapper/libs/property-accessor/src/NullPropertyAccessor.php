<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;

/**
 * Accessor for which no property is readable or writable.
 *
 * @template T of object
 *
 * @template-implements PropertyAccessorInterface<T>
 */
final class NullPropertyAccessor implements PropertyAccessorInterface
{
    public function __construct(
        /**
         * @var T
         */
        private readonly object $object,
    ) {}

    /**
     * @throws PropertyNotReadableException in case of any property is read
     */
    public function read(string $property): mixed
    {
        throw PropertyNotReadableException::becauseReadingIsDisabled($this->object, $property);
    }

    public function isReadable(string $property): bool
    {
        return false;
    }

    /**
     * @throws PropertyNotWritableException in case of any property is written
     */
    public function write(string $property, mixed $value): void
    {
        throw PropertyNotWritableException::becauseWritingIsDisabled($this->object, $property);
    }

    public function isWritable(string $property): bool
    {
        return false;
    }
}
