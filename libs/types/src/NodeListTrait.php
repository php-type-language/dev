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
 * @phpstan-require-extends Node
 * @phpstan-require-implements \ArrayAccess
 * @phpstan-require-implements \IteratorAggregate
 * @phpstan-require-implements \Countable
 */
trait NodeListTrait
{
    /**
     * @var (TNonEmpty is true ? non-empty-list<TNode> : list<TNode>)
     */
    public array $children;

    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        return isset($this->children[$offset]);
    }

    /**
     * @return TNode|null
     */
    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        return $this->children[$offset] ?? null;
    }

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

        // @phpstan-ignore-next-line : An offset beyond the end leaves a gap, the list is renumbered below
        $this->children[$offset] = $value;

        if (!\array_is_list($this->children)) {
            $this->children = \array_values($this->children);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException('An $offset must be an int<0, max>');
        }

        // @phpstan-ignore-next-line : Unset leaves a gap, the list is renumbered below
        unset($this->children[$offset]);

        if (!\array_is_list($this->children)) {
            // @phpstan-ignore-next-line : The callers keep the size the list is declared with
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
