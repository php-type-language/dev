<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidDefaultValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\DefaultValueFactory;

/**
 * Sets the default value from the `default` key: {@see null}, a scalar, an
 * enum case or an array of them.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class DefaultConfigLoader implements PropertyConfigLoaderInterface
{
    private const KEY = 'default';

    public function getKeys(): array
    {
        return [self::KEY];
    }

    /**
     * @throws InvalidDefaultValueException in case of the value is an object
     *         other than an enum case
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata
    {
        if (!$config->has(self::KEY)) {
            return $property;
        }

        $value = DefaultValueFactory::createFromValue($config->get(self::KEY), $config->createContext(self::KEY));

        return $property->withDefaultValue($value);
    }
}
