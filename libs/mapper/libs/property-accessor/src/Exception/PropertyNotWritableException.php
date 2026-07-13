<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

class PropertyNotWritableException extends PropertyAccessException
{
    public static function becausePropertyIsNotWritable(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The %s::$%s property is not writable', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
