<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The property exists, but its value cannot be read.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class PropertyNotReadableException extends PropertyAccessException
{
    public static function becausePropertyIsWriteOnly(object $object, string $property): self
    {
        $message = \sprintf('Property %s is write-only', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }

    public static function becauseReadingIsDisabled(object $object, string $property): self
    {
        $message = \sprintf('Reading of property %s is disabled', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }

    public static function becauseReadingFailed(object $object, string $property, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Property %s cannot be read', self::formatProperty($object, $property));

        if ($previous !== null) {
            $message .= ': ' . $previous->getMessage();
        }

        return new self($object, $property, $message, $previous);
    }
}
