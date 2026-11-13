<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Metadata;

/**
 * Mapping of a class: its mapped properties.
 *
 * @template T of object
 */
final class ClassMetadata
{
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

        return new self($this->name, $properties);
    }
}
