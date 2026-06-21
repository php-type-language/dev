<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Description;

use TypeLang\DocBlock\Tag\TagInterface;

/**
 * @phpstan-import-type TaggedDescriptionComponentType from TaggedDescriptionInterface
 *
 * @template-implements \ArrayAccess<array-key, TaggedDescriptionComponentType|null>
 * @template-implements \IteratorAggregate<array-key, TaggedDescriptionComponentType>
 */
final class TaggedDescription implements
    TaggedDescriptionInterface,
    \IteratorAggregate,
    \ArrayAccess
{
    /**
     * @var list<TaggedDescriptionComponentType>
     */
    public readonly array $components;

    /**
     * @var list<TagInterface>
     */
    public array $tags {
        get => $this->tags ??= $this->only(TagInterface::class);
    }

    /**
     * @param iterable<array-key, TagInterface|DescriptionInterface> $components
     */
    public function __construct(iterable $components = [])
    {
        $this->components = \iterator_to_array($components, false);
    }

    /**
     * @template TArgComponent of TaggedDescriptionComponentType
     * @param class-string<TArgComponent> $component
     * @return list<TArgComponent>
     */
    private function only(string $component): array
    {
        $result = [];

        foreach ($this->components as $actual) {
            if ($actual instanceof $component) {
                $result[] = $actual;
            }
        }

        return $result;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->components[$offset]);
    }

    public function offsetGet(mixed $offset): TagInterface|DescriptionInterface|null
    {
        return $this->components[$offset] ?? null;
    }

    /**
     * @throws \BadMethodCallException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException(self::class . ' objects are immutable');
    }

    /**
     * @throws \BadMethodCallException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException(self::class . ' objects are immutable');
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->components);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->components);
    }

    public function __toString(): string
    {
        $result = [];

        foreach ($this->components as $component) {
            $result[] = $component instanceof TagInterface
                ? \sprintf('{%s}', $component)
                : $component;
        }

        return \implode('', $result);
    }
}
