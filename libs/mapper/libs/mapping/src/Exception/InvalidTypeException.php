<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Exception;

use TypeLang\Mapper\Mapping\Metadata\SourceInfo;

/**
 * Type of a property cannot be read by its source.
 */
final class InvalidTypeException extends MetadataException
{
    /**
     * @param string $property property reference: `App\User::$id`
     * @param non-empty-string $reason reason the type cannot be read
     */
    public static function becauseTypeCannotBeRead(string $property, string $reason, SourceInfo $source): self
    {
        return new self($property, $source, $reason);
    }
}
