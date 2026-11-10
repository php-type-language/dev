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
 *
 * @method (TNonEmpty is true ? int<1, max> : int<0, max>) count()
 */
abstract class NodesListNode extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /** @template-use NodeListTrait<TNode, TNonEmpty> */
    use NodeListTrait;

    /**
     * @param iterable<mixed, TNode> $items
     * @param int<0, max> $offset
     */
    public function __construct(iterable $items = [], int $offset = 0)
    {
        $this->children = match (true) {
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
        return $this->children[0] ?? null;
    }

    /**
     * The last node, or {@see null} in case of the list is empty.
     *
     * @return (TNonEmpty is true ? TNode : TNode|null)
     */
    public function last(): ?Node
    {
        $key = \array_key_last($this->children);

        if ($key === null) {
            return null;
        }

        return $this->children[$key];
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
        $index = \array_search($node, $this->children, true);

        if (\is_int($index)) {
            return $index;
        }

        return null;
    }

    public function __clone()
    {
        foreach ($this->children as $index => $node) {
            $this->children[$index] = clone $node;
        }
    }
}
