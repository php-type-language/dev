<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

final class StaticPropertyReadException extends PropertyNotReadableException
{
    public static function becausePropertyIsStatic(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The static %s::$%s property cannot be read', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
