<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Exception;

/**
 * The constructor or the static method has failed or returned an invalid
 * value.
 */
final class ConstructionFailedException extends InstantiationException
{
    public static function becauseConstructorHasThrown(string $class, \Throwable $previous): self
    {
        $message = \sprintf(
            'Constructor of class "%s" has thrown an exception: %s',
            \addcslashes($class, '"'),
            $previous->getMessage(),
        );

        return new self($class, $message, $previous);
    }

    public static function becauseMethodHasThrown(string $class, string $method, \Throwable $previous): self
    {
        $message = \sprintf(
            'Method "%s" has thrown an exception: %s',
            self::formatMethod($class, $method),
            $previous->getMessage(),
        );

        return new self($class, $message, $previous);
    }

    public static function becauseMethodReturnedInvalidValue(string $class, string $method, mixed $value): self
    {
        $message = \sprintf(
            'Method "%s" must return an instance of "%s", %s returned',
            self::formatMethod($class, $method),
            \addcslashes($class, '"'),
            \get_debug_type($value),
        );

        return new self($class, $message);
    }
}
