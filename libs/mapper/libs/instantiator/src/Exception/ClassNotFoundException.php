<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Exception;

/**
 * The class, interface, trait or enum with the given name does not exist.
 */
final class ClassNotFoundException extends InstantiationException
{
    public static function becauseClassDoesNotExist(string $class): self
    {
        $message = \sprintf('Class "%s" does not exist', \addcslashes($class, '"'));

        return new self($class, $message);
    }
}
