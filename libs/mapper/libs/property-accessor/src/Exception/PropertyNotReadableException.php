<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

class PropertyNotReadableException extends PropertyAccessException
{
    public static function becausePropertyIsNotReadable(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The %s::$%s property is not readable', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
