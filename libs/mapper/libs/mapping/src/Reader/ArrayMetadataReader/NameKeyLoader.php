<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;

/**
 * Sets the external name from the `name` config key.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class NameKeyLoader implements KeyLoaderInterface
{
    /**
     * @throws InvalidValueException in case of the value is not a non-empty
     *         string
     */
    public function load(mixed $value, PropertyMetadata $property, SourceContext $context): PropertyMetadata
    {
        if (!\is_string($value) || $value === '') {
            throw InvalidValueException::becauseValueIsNotNonEmptyString(
                'name',
                $context->target,
                $context->source,
            );
        }

        return $property->withExternalName($value);
    }
}
