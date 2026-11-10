<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The property is read before a value has been assigned to it.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class UninitializedPropertyException extends PropertyAccessException
{
    public static function becausePropertyIsNotInitialized(object $object, string $property): self
    {
        $message = \sprintf('Property %s is not initialized', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }
}
