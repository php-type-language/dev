<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\MetadataException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;

/**
 * Loads its keys of a property config.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
interface PropertyConfigLoaderInterface
{
    /**
     * Returns the keys of a property config the loader reads.
     *
     * @return non-empty-list<non-empty-string>
     */
    public function getKeys(): array;

    /**
     * Returns the property with the values of the keys of the loader, the
     * property is returned as is in case of the config has none of them.
     *
     * @throws MetadataException in case of a value is wrong
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata;
}
