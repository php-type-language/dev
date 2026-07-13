<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

/**
 * Occurs when writing a virtual property that has no set hook, i.e. a property
 * that can only be read.
 */
final class ReadOnlyPropertyException extends PropertyNotWritableException
{
    public static function becausePropertyIsReadOnly(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The read-only %s::$%s property cannot be written', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
