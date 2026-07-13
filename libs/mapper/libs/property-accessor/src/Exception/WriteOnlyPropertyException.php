<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

/**
 * Occurs when reading a virtual property that has no get hook, i.e. a property
 * that can only be written.
 */
final class WriteOnlyPropertyException extends PropertyNotReadableException
{
    public static function becausePropertyIsWriteOnly(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The write-only %s::$%s property cannot be read', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
