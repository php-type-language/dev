<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Exception;

/**
 * The constructor or the static method cannot be used to create an object.
 */
final class MethodNotCallableException extends InstantiationException
{
    public static function becauseConstructorIsNotPublic(string $class): self
    {
        $message = \sprintf('Constructor of class "%s" is not public', \addcslashes($class, '"'));

        return new self($class, $message);
    }

    public static function becauseMethodDoesNotExist(string $class, string $method): self
    {
        $message = \sprintf('Method "%s" does not exist', self::formatMethod($class, $method));

        return new self($class, $message);
    }

    public static function becauseMethodIsNotPublic(string $class, string $method): self
    {
        $message = \sprintf('Method "%s" is not public', self::formatMethod($class, $method));

        return new self($class, $message);
    }

    public static function becauseMethodIsNotStatic(string $class, string $method): self
    {
        $message = \sprintf('Method "%s" is not static', self::formatMethod($class, $method));

        return new self($class, $message);
    }

    public static function becauseMethodIsAbstract(string $class, string $method): self
    {
        $message = \sprintf('Method "%s" is abstract', self::formatMethod($class, $method));

        return new self($class, $message);
    }
}
