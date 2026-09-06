<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template TNode of Node = Node
 *
 * @template-implements \IteratorAggregate<array-key, TNode>
 * @template-implements \ArrayAccess<int<0, max>, TNode>
 */
abstract class NodeList extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @var list<TNode>
     */
    public array $items = [];

    /**
     * @param iterable<mixed, TNode> $items
     * @param int<0, max> $offset
     */
    public function __construct(iterable $items = [], int $offset = 0)
    {
        $this->items = match (true) {
            // A list is already shaped the way it is stored, so it is taken
            // as it is rather than copied
            \is_array($items) && \array_is_list($items) => $items,
            \is_array($items) => \array_values($items),
            default => \iterator_to_array($items, false),
        };

        parent::__construct($offset);
    }

    /**
     * Returns the ordinal number (position) of an element {@see TNode} in
     * a node list, starting with index 0.
     *
     * Returns {@see null} if the element {@see TNode} does not belong
     * to the node list.
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
            throw new \InvalidArgumentException('A NodeList index must be an int<0, max>');
        }

        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('A NodeList index must be an int<0, max>');
        }

        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Node) {
            throw new \InvalidArgumentException('A NodeList value must be instance of Node');
        }

        if ($offset === null) {
            $this->items[] = $value;

            return;
        }

        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('A NodeList index must be an int<0, max>|null');
        }

        // @phpstan-ignore-next-line
        $this->items[$offset] = $value;

        if (!\array_is_list($this->items)) {
            $this->items = \array_values($this->items);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('A NodeList index must be an int<0, max>|null');
        }

        // @phpstan-ignore-next-line
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
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * Gets the first node of a list or {@see null} in case of the list is empty.
     *
     * @return TNode|null
     */
    public function first(): ?Node
    {
        return $this->items[0] ?? null;
    }

    /**
     * Gets the last node of a list or {@see null} in case of the list is empty.
     *
     * @return TNode|null
     */
    public function last(): ?Node
    {
        $key = \array_key_last($this->items);

        if ($key === null) {
            return null;
        }

        return $this->items[$key];
    }
}
