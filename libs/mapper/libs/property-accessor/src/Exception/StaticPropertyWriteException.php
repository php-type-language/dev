<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

final class StaticPropertyWriteException extends PropertyNotWritableException
{
    public static function becausePropertyIsStatic(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The static %s::$%s property cannot be written', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
