<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The written value does not match the declared type of the property.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class InvalidPropertyValueException extends PropertyAccessException
{
    public static function becauseValueTypeIsInvalid(
        object $object,
        string $property,
        string $expected,
        mixed $value,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf(
            'Property %s of type %s cannot be assigned a value of type %s',
            self::formatProperty($object, $property),
            $expected,
            \get_debug_type($value),
        );

        return new self($object, $property, $message, $previous);
    }
}
