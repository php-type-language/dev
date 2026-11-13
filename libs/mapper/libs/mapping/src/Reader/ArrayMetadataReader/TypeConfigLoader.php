<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeConfigLoader\TypeDirection;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Sets the types from the `type`, `read_type` and `write_type` keys and their
 * strictness from the `strict` key: of the types of the config, or of the
 * types of the lower readers in case of the config sets no type.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class TypeConfigLoader implements PropertyConfigLoaderInterface
{
    private const STRICT_KEY = 'strict';

    public function __construct(
        /**
         * @var TypeParserInterface<TypeNode, string>
         */
        private readonly TypeParserInterface $parser,
    ) {}

    public function getKeys(): array
    {
        return [
            TypeDirection::Both->value,
            TypeDirection::Read->value,
            TypeDirection::Write->value,
            self::STRICT_KEY,
        ];
    }

    /**
     * @throws InvalidValueException in case of a type is not a non-empty
     *         string or the strictness is not a boolean
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata
    {
        $isRead = false;
        $isWrite = false;

        foreach (TypeDirection::cases() as $direction) {
            if (!$config->has($direction->value)) {
                continue;
            }

            $type = $this->parseType($config, $direction->value);

            if ($direction->isRead()) {
                $property = $property->withReadType($type);
                $isRead = true;
            }

            if ($direction->isWrite()) {
                $property = $property->withWriteType($type);
                $isWrite = true;
            }
        }

        if (!$config->has(self::STRICT_KEY)) {
            return $property;
        }

        // strict: true
        if (!$isRead && !$isWrite) {
            $isRead = true;
            $isWrite = true;
        }

        return self::loadStrict($config, $property, $isRead, $isWrite);
    }

    /**
     * @param non-empty-string $key
     * @throws InvalidValueException in case of the type is not a non-empty
     *         string
     */
    private function parseType(PropertyConfig $config, string $key): TypeMetadata
    {
        $value = $config->get($key);
        $context = $config->createContext($key);

        if (!\is_string($value) || $value === '') {
            throw InvalidValueException::becauseValueIsNotNonEmptyString($key, $context->target, $context->source);
        }

        return $context->parseType($this->parser, $value);
    }

    /**
     * @throws InvalidValueException in case of the strictness is not a
     *         boolean
     */
    private static function loadStrict(
        PropertyConfig $config,
        PropertyMetadata $property,
        bool $isRead,
        bool $isWrite,
    ): PropertyMetadata {
        $isStrict = $config->get(self::STRICT_KEY);

        if (!\is_bool($isStrict)) {
            $context = $config->createContext(self::STRICT_KEY);

            throw InvalidValueException::becauseValueIsNotBoolean(self::STRICT_KEY, $context->target, $context->source);
        }

        if ($isRead && $property->read !== null) {
            $property = $property->withReadType($property->read->withStrict($isStrict));
        }

        if ($isWrite && $property->write !== null) {
            $property = $property->withWriteType($property->write->withStrict($isStrict));
        }

        return $property;
    }
}
