<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Exception;

use TypeLang\Mapper\Mapping\Metadata\SourceInfo;

/**
 * Attribute of a property cannot be created.
 */
final class InvalidAttributeException extends MetadataException
{
    /**
     * @param string $property property reference: `App\User::$id`
     */
    public static function becauseAttributeCannotBeCreated(
        string $attribute,
        string $property,
        SourceInfo $source,
        \Throwable $previous,
    ): self {
        $message = \sprintf('attribute %s cannot be created (%s)', $attribute, $previous->getMessage());

        return new self($property, $source, $message, $previous);
    }

    /**
     * @param string $method method reference: `App\User::getId()`
     */
    public static function becauseAttributeIsOnMethod(string $attribute, string $method, SourceInfo $source): self
    {
        $message = \sprintf('attribute %s is allowed only on a property or its hook', $attribute);

        return new self($method, $source, $message);
    }
}
