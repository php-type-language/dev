<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Metadata;

use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;

/**
 * Mapping of one property of a class.
 */
final class PropertyMetadata
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        /**
         * Name of the property in mapped data, {@see null} in case of it
         * matches the PHP name
         *
         * @var non-empty-string|null
         */
        public readonly ?string $externalName = null,
        /**
         * Type of the value read from the property on normalization
         */
        public readonly ?TypeMetadata $read = null,
        /**
         * Type of the value written to the property on denormalization
         */
        public readonly ?TypeMetadata $write = null,
        /**
         * Value used when mapped data has none, {@see null} in case of the
         * value is required
         */
        public readonly ?DefaultValueMetadata $default = null,
    ) {}

    /**
     * @return non-empty-string
     */
    public function getOutputName(): string
    {
        return $this->externalName ?? $this->name;
    }

    /**
     * Returns the read type, the write type in case of the read one is not
     * set, or `mixed` in case of none is set.
     */
    public function getReadType(): TypeMetadata
    {
        return $this->read
            ?? $this->write
            ?? self::createMixedType();
    }

    /**
     * Returns the write type, the read type in case of the write one is not
     * set, or `mixed` in case of none is set.
     */
    public function getWriteType(): TypeMetadata
    {
        return $this->write
            ?? $this->read
            ?? self::createMixedType();
    }

    /**
     * @param non-empty-string $name
     */
    public function withExternalName(string $name): self
    {
        return new self(
            name: $this->name,
            externalName: $name,
            read: $this->read,
            write: $this->write,
            default: $this->default,
        );
    }

    public function withType(TypeMetadata $type): self
    {
        return new self(
            name: $this->name,
            externalName: $this->externalName,
            read: $type,
            write: $type,
            default: $this->default,
        );
    }

    public function withReadType(TypeMetadata $type): self
    {
        return new self(
            name: $this->name,
            externalName: $this->externalName,
            read: $type,
            write: $this->write,
            default: $this->default,
        );
    }

    public function withWriteType(TypeMetadata $type): self
    {
        return new self(
            name: $this->name,
            externalName: $this->externalName,
            read: $this->read,
            write: $type,
            default: $this->default,
        );
    }

    public function withDefaultValue(DefaultValueMetadata $value): self
    {
        return new self(
            name: $this->name,
            externalName: $this->externalName,
            read: $this->read,
            write: $this->write,
            default: $value,
        );
    }

    private static function createMixedType(): TypeMetadata
    {
        return TypeMetadata::createFromType(
            type: new NamedTypeNode(new Name('mixed')),
            source: new SourceInfo(),
        );
    }
}
