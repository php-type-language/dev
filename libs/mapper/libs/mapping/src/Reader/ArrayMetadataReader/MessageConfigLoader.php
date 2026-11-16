<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;

/**
 * Sets the messages of the property from the `type_error_message` and
 * `undefined_error_message` keys.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class MessageConfigLoader implements PropertyConfigLoaderInterface
{
    private const TYPE_KEY = 'type_error_message';

    private const UNDEFINED_KEY = 'undefined_error_message';

    public function getKeys(): array
    {
        return [self::TYPE_KEY, self::UNDEFINED_KEY];
    }

    /**
     * @throws InvalidValueException in case of a message is not a non-empty
     *         string
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata
    {
        $type = self::findMessage($config, self::TYPE_KEY);

        if ($type !== null) {
            $property = $property->withTypeErrorMessage($type);
        }

        $undefined = self::findMessage($config, self::UNDEFINED_KEY);

        if ($undefined !== null) {
            $property = $property->withUndefinedErrorMessage($undefined);
        }

        return $property;
    }

    /**
     * @param non-empty-string $key
     * @return non-empty-string|null
     * @throws InvalidValueException in case of the message is not a
     *         non-empty string
     */
    private static function findMessage(PropertyConfig $config, string $key): ?string
    {
        if (!$config->has($key)) {
            return null;
        }

        $value = $config->get($key);

        if (!\is_string($value) || $value === '') {
            $context = $config->createContext($key);

            throw InvalidValueException::becauseValueIsNotNonEmptyString($key, $context->target, $context->source);
        }

        return $value;
    }
}
