<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * Several types joined by an operator, like a union or an intersection.
 *
 * A type of the same kind is flattened into its parent: an `A|(B|C)`
 * holds the three types side by side.
 *
 * The flattening and the minimum of two types are checked on creation
 * only. Since PHP 8.4, property hooks make it possible to keep the checks
 * permanent.
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-implements \IteratorAggregate<array-key, T>
 * @template-implements \ArrayAccess<int<0, max>, T>
 */
abstract class LogicalTypeNode extends TypeNode implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @var list{T, T, ...<T>}
     */
    public array $types;

    /**
     * @param iterable<mixed, T> $types
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of less than two types are passed
     */
    public function __construct(
        iterable $types,
        int $offset = 0,
    ) {
        $types = self::unwrap($types);

        if (\count($types) < 2) {
            throw new \InvalidArgumentException('A logical type must contain at least 2 types');
        }

        // @phpstan-ignore-next-line : The list contains at least 2 types, checked above
        $this->types = $types;

        parent::__construct($offset);
    }

    /**
     * Flattens the types of the same kind into a single list.
     *
     * @param iterable<TypeNode> $types
     * @return list<TypeNode>
     */
    private static function unwrap(iterable $types): array
    {
        $result = [];

        foreach ($types as $type) {
            if ($type instanceof static) {
                foreach (self::unwrap($type->types) as $child) {
                    $result[] = $child;
                }

                continue;
            }

            $result[] = $type;
        }

        return $result;
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetExists($offset) must be an int<0, max>', static::class),
            );
        }

        return isset($this->types[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetGet($offset) must be an int<0, max>', static::class),
            );
        }

        return $this->types[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Node) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet(..., $value) must be instance of %s', static::class, Node::class),
            );
        }

        if ($offset === null) {
            $this->types[] = $value;

            return;
        }

        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet($offset, ...) must be an int<0, max>|null', static::class),
            );
        }

        // @phpstan-ignore-next-line : An offset beyond the end leaves a gap, the list is renumbered below
        $this->types[$offset] = $value;

        if (!\array_is_list($this->types)) {
            $this->types = \array_values($this->types);
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
        unset($this->types[$offset]);

        if (\count($this->types) < 2) {
            throw new \InvalidArgumentException('A logical type must contain at least 2 types');
        }

        if (!\array_is_list($this->types)) {
            // @phpstan-ignore-next-line : The size is checked above, renumbering keeps it
            $this->types = \array_values($this->types);
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->types);
    }

    /**
     * @return int<2, max> a logical type must contain at least 2 types
     */
    public function count(): int
    {
        /** @var int<2, max> */
        return \count($this->types);
    }
}
