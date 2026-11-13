<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidDefaultValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\DefaultValueFactory;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;

/**
 * Sets the default value from the `default` config key: {@see null}, a
 * scalar, an enum case or an array of them.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class DefaultKeyLoader implements KeyLoaderInterface
{
    /**
     * @throws InvalidDefaultValueException in case of the value is an object
     *         other than an enum case
     */
    public function load(mixed $value, PropertyMetadata $property, SourceContext $context): PropertyMetadata
    {
        return $property->withDefaultValue(DefaultValueFactory::createFromValue($value, $context));
    }
}
