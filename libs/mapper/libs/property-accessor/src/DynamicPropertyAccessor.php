<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;

/**
 * Reads and writes dynamic properties, such as properties of
 * {@see \stdClass}. A missing property is created on write.
 *
 * @template T of object
 *
 * @template-extends PropertyAccessor<T>
 */
final class DynamicPropertyAccessor extends PropertyAccessor
{
    public function read(string $property): mixed
    {
        if (!\property_exists($this->object, $property)) {
            throw PropertyNotFoundException::becausePropertyDoesNotExist($this->object, $property);
        }

        // @phpstan-ignore-next-line dynamic properties are accessed by name
        return $this->object->{$property};
    }

    public function isReadable(string $property): bool
    {
        return \property_exists($this->object, $property);
    }

    public function write(string $property, mixed $value): void
    {
        // @phpstan-ignore-next-line dynamic properties are accessed by name
        $this->object->{$property} = $value;
    }

    public function isWritable(string $property): bool
    {
        return true;
    }
}
