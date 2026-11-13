<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\MetadataException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;

/**
 * Loads one key of a property config.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
interface KeyLoaderInterface
{
    /**
     * Returns the property with the value of the key.
     *
     * @throws MetadataException in case of the value is wrong
     */
    public function load(mixed $value, PropertyMetadata $property, SourceContext $context): PropertyMetadata;
}
