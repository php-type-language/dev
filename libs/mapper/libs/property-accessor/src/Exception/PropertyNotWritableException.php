<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The property exists, but a value cannot be written to it.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class PropertyNotWritableException extends PropertyAccessException
{
    public static function becausePropertyIsReadOnly(object $object, string $property): self
    {
        $message = \sprintf('Property %s is read-only', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }

    public static function becauseWritingIsDisabled(object $object, string $property): self
    {
        $message = \sprintf('Writing of property %s is disabled', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }

    public static function becauseWritingFailed(object $object, string $property, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Property %s cannot be written', self::formatProperty($object, $property));

        if ($previous !== null) {
            $message .= ': ' . $previous->getMessage();
        }

        return new self($object, $property, $message, $previous);
    }
}
