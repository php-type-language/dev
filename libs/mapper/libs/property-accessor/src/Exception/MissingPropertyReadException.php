<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

final class MissingPropertyReadException extends PropertyNotReadableException
{
    public static function becausePropertyDoesNotExist(object $ctx, string $property, ?\Throwable $prev = null): self
    {
        $message = \sprintf('The %s::$%s property does not exist and cannot be read', $ctx::class, $property);

        return new self($ctx, $property, $message, previous: $prev);
    }
}
