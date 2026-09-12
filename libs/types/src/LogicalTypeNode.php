<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * Several types joined by an operator, like a union or an intersection.
 *
 * A statement of the same kind is flattened into its parent, so an
 * `A|(B|C)` holds the three of them side by side rather than a nested
 * statement.
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
    public array $statements;

    /**
     * A logical statement accepts an arbitrary number of types, so there is no
     * place left for an offset argument: use the {@see $offset} property.
     *
     * @param iterable<mixed, T> $statements
     * @param int<0, max> $offset
     * @throws \LogicException in case of less than two statements are passed
     */
    public function __construct(
        iterable $statements,
        int $offset = 0,
    ) {
        $statements = self::unwrap($statements);

        if (\count($statements) < 2) {
            throw new \InvalidArgumentException('A logical statement must contain at least 2 elements');
        }

        // @phpstan-ignore-next-line : List of types contains at least 2 elements
        $this->statements = $statements;

        parent::__construct($offset);
    }

    /**
     * Flattens the statements of the same kind into a single list, so that
     * a logical statement never contains a statement of its own kind.
     *
     * @param iterable<TypeNode> $statements
     * @return list<TypeNode>
     */
    private static function unwrap(iterable $statements): array
    {
        $result = [];

        foreach ($statements as $statement) {
            if ($statement instanceof static) {
                foreach (self::unwrap($statement->statements) as $child) {
                    $result[] = $child;
                }

                continue;
            }

            $result[] = $statement;
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

        return isset($this->statements[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetGet($offset) must be an int<0, max>', static::class),
            );
        }

        return $this->statements[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Node) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet(..., $value) must be instance of %s', static::class, Node::class),
            );
        }

        if ($offset === null) {
            $this->statements[] = $value;

            return;
        }

        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetSet($offset, ...) must be an int<0, max>|null', static::class),
            );
        }

        // @phpstan-ignore-next-line
        $this->statements[$offset] = $value;

        if (!\array_is_list($this->statements)) {
            $this->statements = \array_values($this->statements);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) || $offset < 0) {
            throw new \InvalidArgumentException(
                \sprintf('A %s::offsetUnset($offset) must be an int<0, max>', static::class),
            );
        }

        // @phpstan-ignore-next-line : Temporary allow stmt removing
        unset($this->statements[$offset]);

        if (\count($this->statements) < 2) {
            throw new \InvalidArgumentException('A logical statement must contain at least 2 elements');
        }

        if (!\array_is_list($this->statements)) {
            // @phpstan-ignore-next-line : An array size already has been checked above
            $this->statements = \array_values($this->statements);
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->statements);
    }

    /**
     * @return int<2, max> a logical statement must contain at least 2 elements
     */
    public function count(): int
    {
        /** @var int<2, max> */
        return \count($this->statements);
    }
}
