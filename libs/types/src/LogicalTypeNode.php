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
 * only.
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-implements \IteratorAggregate<int<0, max>, T>
 * @template-implements \ArrayAccess<int<0, max>, T>
 *
 * @property list{T, T, ...<T>} $children
 *
 * @method int<2, max> count()
 */
abstract class LogicalTypeNode extends TypeNode implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /** @template-use NodeListTrait<T, true> */
    use NodeListTrait {
        offsetUnset as private unsetNode;
    }

    /**
     * @param iterable<mixed, T> $types
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of less than two types are
     *         passed
     */
    public function __construct(
        iterable $types,
        int $offset = 0,
    ) {
        $types = self::unwrap($types);

        if (\count($types) < 2) {
            throw new \InvalidArgumentException('A logical type must contain at least 2 types');
        }

        // @phpstan-ignore-next-line at least 2 types, checked above
        $this->children = $types;

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
                foreach (self::unwrap($type->children) as $child) {
                    $result[] = $child;
                }

                continue;
            }

            $result[] = $type;
        }

        return $result;
    }

    /**
     * @throws \InvalidArgumentException in case of the offset is not an
     *         `int<0, max>` or less than two types are left
     */
    public function offsetUnset(mixed $offset): void
    {
        $previous = $this->children;

        $this->unsetNode($offset);

        if (\count($this->children) < 2) {
            $this->children = $previous;

            throw new \InvalidArgumentException('A logical type must contain at least 2 types');
        }
    }

    public function __clone()
    {
        foreach ($this->children as $index => $node) {
            $this->children[$index] = clone $node;
        }
    }
}
