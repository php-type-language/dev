<?php

declare(strict_types=1);

namespace TypeLang\DocBlock;

use TypeLang\DocBlock\Description\Description;
use TypeLang\DocBlock\Description\DescriptionInterface;
use TypeLang\DocBlock\Description\OptionalDescriptionProviderInterface;
use TypeLang\DocBlock\Tag\TagInterface;
use TypeLang\DocBlock\Tag\TagsProviderInterface;

/**
 * An implementation represents a structure containing a description and a set
 * of tags that describe an arbitrary DocBlock Comment in the code.
 *
 * @template-implements \ArrayAccess<array-key, TagInterface|null>
 * @template-implements \IteratorAggregate<array-key, TagInterface>
 */
final readonly class DocBlock implements
    OptionalDescriptionProviderInterface,
    TagsProviderInterface,
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    public ?DescriptionInterface $description;

    /**
     * @var list<TagInterface>
     */
    public array $tags;

    /**
     * @param iterable<array-key, TagInterface> $tags List of all tags contained in
     *        a docblock object.
     *
     *        Note that the constructor can receive an arbitrary iterator, like
     *        {@see \Traversable} or {@see array}, but the object itself
     *        contains the directly generated list ({@see array}} of
     *        {@see TagInterface} objects.
     */
    public function __construct(
        \Stringable|string|null $description = null,
        iterable $tags = [],
    ) {
        $this->description = Description::tryCreateFromStringOrNull($description);
        $this->tags = \iterator_to_array($tags, false);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->tags[$offset]);
    }

    public function offsetGet(mixed $offset): ?TagInterface
    {
        return $this->tags[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException(self::class . ' objects are immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException(self::class . ' objects are immutable');
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->tags);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->tags);
    }
}
