<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * Access to the nodes of a node that holds an ordered list of them: by
 * offset, by iteration and by count.
 *
 * @template TNode of Node
 * @template TNonEmpty of bool = false
 *
 * @phpstan-require-implements \ArrayAccess
 * @phpstan-require-implements \IteratorAggregate
 * @phpstan-require-implements \Countable
 * @phpstan-require-extends Node
 */
trait NodeListTrait
{
    /**
     * @var (TNonEmpty is true ? non-empty-list<TNode> : list<TNode>)
     */
    public array $children;

    /**
     * @throws \InvalidArgumentException in case of the offset is not an
     *         `int<0, max>`
     */
    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        return isset($this->children[$offset]);
    }

    /**
     * @return TNode|null
     * @throws \InvalidArgumentException in case of the offset is not an
     *         `int<0, max>`
     */
    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        return $this->children[$offset] ?? null;
    }

    /**
     * @throws \InvalidArgumentException in case of the value is not a
     *         {@see Node} or the offset is not an `int<0, max>|null`
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Node) {
            throw new \InvalidArgumentException(\sprintf('A $value must be an instance of %s', Node::class));
        }

        if ($offset === null) {
            $this->children[] = $value;

            return;
        }

        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>|null');
        }

        // @phpstan-ignore-next-line a gap past the end is renumbered below
        $this->children[$offset] = $value;

        if (!\array_is_list($this->children)) {
            $this->children = \array_values($this->children);
        }
    }

    /**
     * @throws \InvalidArgumentException in case of the offset is not an
     *         `int<0, max>`
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        // @phpstan-ignore-next-line the gap is renumbered below
        unset($this->children[$offset]);

        if (!\array_is_list($this->children)) {
            // @phpstan-ignore-next-line callers keep the declared list size
            $this->children = \array_values($this->children);
        }
    }

    /**
     * @return \Traversable<int<0, max>, TNode>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->children);
    }
}
