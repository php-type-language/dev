<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

/**
 * Source of the docblock types of a property.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
interface PropertyTypeProviderInterface
{
    public function readTypes(PropertyContext $property): PropertyTypes;
}
