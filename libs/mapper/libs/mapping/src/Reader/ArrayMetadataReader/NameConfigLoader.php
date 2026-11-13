<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;

/**
 * Sets the external name from the `name` key.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class NameConfigLoader implements PropertyConfigLoaderInterface
{
    private const KEY = 'name';

    public function getKeys(): array
    {
        return [self::KEY];
    }

    /**
     * @throws InvalidValueException in case of the value is not a non-empty
     *         string
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata
    {
        if (!$config->has(self::KEY)) {
            return $property;
        }

        $value = $config->get(self::KEY);

        if (!\is_string($value) || $value === '') {
            $context = $config->createContext(self::KEY);

            throw InvalidValueException::becauseValueIsNotNonEmptyString(self::KEY, $context->target, $context->source);
        }

        return $property->withExternalName($value);
    }
}
