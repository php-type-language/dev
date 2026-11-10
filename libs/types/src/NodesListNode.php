<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * An ordered list of nodes of one kind, like the arguments of a generic
 * or the fields of a shape.
 *
 * @template TNode of Node = Node
 * @template TNonEmpty of bool = false
 *
 * @template-implements \IteratorAggregate<array-key, TNode>
 * @template-implements \ArrayAccess<int<0, max>, TNode>
 */
abstract class NodesListNode extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @var (TNonEmpty is true ? non-empty-list<TNode> : list<TNode>)
     */
    public array $items = [];

    /**
     * @param iterable<mixed, TNode> $items
     * @param int<0, max> $offset
     */
    public function __construct(iterable $items = [], int $offset = 0)
    {
        $this->items = match (true) {
            $items instanceof \Traversable => \iterator_to_array($items, false),
            \array_is_list($items) => $items,
            default => \array_values($items),
        };

        parent::__construct($offset);
    }

    /**
     * The first node, or {@see null} in case of the list is empty.
     *
     * @return (TNonEmpty is true ? TNode : TNode|null)
     */
    public function first(): ?Node
    {
        return $this->items[0] ?? null;
    }

    /**
     * The last node, or {@see null} in case of the list is empty.
     *
     * @return (TNonEmpty is true ? TNode : TNode|null)
     */
    public function last(): ?Node
    {
        $key = \array_key_last($this->items);

        if ($key === null) {
            return null;
        }

        return $this->items[$key];
    }

    /**
     * The position of a node in the list, or {@see null} in case of the
     * node does not belong to it.
     *
     * @param TNode $node
     * @return int<0, max>|null
     */
    public function findIndex(Node $node): ?int
    {
        $index = \array_search($node, $this->items, true);

        if (\is_int($index)) {
            return $index;
        }

        return null;
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetExists($offset) must be an int<0, max>', static::class),
            );
        }

        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetGet($offset) must be an int<0, max>', static::class),
            );
        }

        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Node) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet(..., $value) must be instance of %s', static::class, Node::class),
            );
        }

        if ($offset === null) {
            $this->items[] = $value;

            return;
        }

        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet($offset, ...) must be an int<0, max>|null', static::class),
            );
        }

        // @phpstan-ignore-next-line : An offset beyond the end leaves a gap, the list is renumbered below
        $this->items[$offset] = $value;

        if (!\array_is_list($this->items)) {
            $this->items = \array_values($this->items);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetUnset($offset) must be an int<0, max>', static::class),
            );
        }

        // @phpstan-ignore-next-line : Unset leaves a gap, the list is renumbered below
        unset($this->items[$offset]);

        if (!\array_is_list($this->items)) {
            $this->items = \array_values($this->items);
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return (TNonEmpty is true ? int<1, max> : int<0, max>)
     */
    public function count(): int
    {
        return \count($this->items);
    }

    public function __clone()
    {
        foreach ($this->items as $index => $node) {
            $this->items[$index] = clone $node;
        }
    }
}
