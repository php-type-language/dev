<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Metadata;

/**
 * Mapping of a class: its mapped properties and its polymorphism.
 *
 * @template T of object
 */
final class ClassMetadata
{
    /**
     * Version of the format of the metadata classes of this namespace, it is
     * raised on each change of their properties, a stored metadata of
     * another version is not read
     */
    public const FORMAT_VERSION = 1;

    public function __construct(
        /**
         * @var class-string<T>
         */
        public readonly string $name,
        /**
         * Mapped properties by PHP name, in order of the output keys
         *
         * @var array<non-empty-string, PropertyMetadata>
         */
        public readonly array $properties = [],
        /**
         * Types the data of the class is mapped to by its tag, {@see null}
         * in case of the class is mapped by its own properties
         */
        public readonly ?DiscriminatorMetadata $discriminator = null,
    ) {}

    /**
     * @param non-empty-string $name
     */
    public function findProperty(string $name): ?PropertyMetadata
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * Returns a copy with the property replacing the property of the same
     * name, a new property is added to the end.
     *
     * @return self<T>
     */
    public function withProperty(PropertyMetadata $property): self
    {
        $properties = $this->properties;
        $properties[$property->name] = $property;

        return new self($this->name, $properties, $this->discriminator);
    }

    /**
     * @return self<T>
     */
    public function withDiscriminator(DiscriminatorMetadata $discriminator): self
    {
        return new self($this->name, $this->properties, $discriminator);
    }
}
