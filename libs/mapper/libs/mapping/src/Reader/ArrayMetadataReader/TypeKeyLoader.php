<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeKeyLoader\TypeDirection;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Sets the read type, the write type or both from a config key: `type`,
 * `read_type`, `write_type`.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class TypeKeyLoader implements KeyLoaderInterface
{
    public function __construct(
        private readonly TypeDirection $direction,
        /**
         * @var TypeParserInterface<TypeNode, string>
         */
        private readonly TypeParserInterface $parser,
    ) {}

    /**
     * @throws InvalidValueException in case of the value is not a non-empty
     *         string
     */
    public function load(mixed $value, PropertyMetadata $property, SourceContext $context): PropertyMetadata
    {
        if (!\is_string($value) || $value === '') {
            throw InvalidValueException::becauseValueIsNotNonEmptyString(
                $this->direction->value,
                $context->target,
                $context->source,
            );
        }

        $type = $context->parseType($this->parser, $value);

        if ($this->direction->isRead()) {
            $property = $property->withReadType($type);
        }

        if ($this->direction->isWrite()) {
            $property = $property->withWriteType($type);
        }

        return $property;
    }
}
