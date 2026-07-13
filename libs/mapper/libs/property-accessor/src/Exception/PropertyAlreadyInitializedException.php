<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

/**
 * Occurs when writing a readonly property that has already been initialized:
 * such a property may be written only once.
 */
final class PropertyAlreadyInitializedException extends PropertyNotWritableException
{
    public static function becausePropertyIsAlreadyInitialized(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $template = 'The readonly %s::$%s property is already initialized and cannot be written twice';

        return new self($ctx, $property, \sprintf($template, $ctx::class, $property), previous: $prev);
    }
}
