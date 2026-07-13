<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

final class UninitializedPropertyReadException extends PropertyNotReadableException
{
    public static function becausePropertyIsNotInitialized(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The %s::$%s property is not initialized and cannot be read', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
