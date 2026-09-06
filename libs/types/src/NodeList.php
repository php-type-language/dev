<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template TNode of Node = Node
 *
 * @template-implements \IteratorAggregate<array-key, TNode>
 * @template-implements \ArrayAccess<int<0, max>, TNode>
 *
 * @property-read TNode|null $first An alias of {@see first()} method.
 * @property-read TNode|null $last An alias of {@see last()} method.
 */
abstract class NodeList extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @var list<non-empty-string>
     */
    private const VIRTUAL_PROPERTIES = [
        'first',
        'last',
    ];

    /**
     * @var list<TNode>
     */
    public array $items = [];

    /**
     * @param iterable<mixed, TNode> $items
     */
    public function __construct(iterable $items = [])
    {
        $this->items = \is_array($items)
            ? \array_values($items)
            : \iterator_to_array($items, false);
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
        // @phpstan-ignore-next-line
        \assert(\is_int($offset) && $offset >= 0);

        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        // @phpstan-ignore-next-line
        \assert(\is_int($offset) && $offset >= 0);

        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // @phpstan-ignore-next-line
        \assert(\is_int($offset) && $offset >= 0);
        // @phpstan-ignore-next-line
        \assert($value instanceof Node);

        // @phpstan-ignore-next-line
        $this->items[$offset] = $value;

        if (!\array_is_list($this->items)) {
            $this->items = \array_values($this->items);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        // @phpstan-ignore-next-line
        \assert(\is_int($offset) && $offset >= 0);

        $items = $this->items;
        unset($items[$offset]);
        $this->items = \array_values($items);
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

    public function __get(string $name): mixed
    {
        return match ($name) {
            'first' => $this->first(),
            'last' => $this->last(),
            default => throw new \OutOfRangeException(
                message: \sprintf('Undefined property %s::$%s', static::class, $name),
            ),
        };
    }

    public function __isset(string $name): bool
    {
        return \in_array($name, self::VIRTUAL_PROPERTIES, true);
    }
}
