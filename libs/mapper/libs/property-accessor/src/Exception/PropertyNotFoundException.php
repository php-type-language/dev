<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * The object has no instance property with the given name.
 *
 * @template TSubject of object = object
 *
 * @template-extends PropertyAccessException<TSubject>
 */
final class PropertyNotFoundException extends PropertyAccessException
{
    public static function becausePropertyDoesNotExist(
        object $object,
        string $property,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('Property %s does not exist', self::formatProperty($object, $property));

        return new self($object, $property, $message, $previous);
    }

    public static function becausePropertyIsStatic(object $object, string $property): self
    {
        $message = \sprintf('Property %s is static', self::formatProperty($object, $property));

        return new self($object, $property, $message);
    }
}
