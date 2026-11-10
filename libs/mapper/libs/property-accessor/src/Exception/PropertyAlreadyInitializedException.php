<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The `readonly` property is written after a value has been assigned to it.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class PropertyAlreadyInitializedException extends PropertyAccessException
{
    public static function becausePropertyIsAlreadyInitialized(object $object, string $property): self
    {
        $message = \sprintf('Readonly property %s is already initialized', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }
}
