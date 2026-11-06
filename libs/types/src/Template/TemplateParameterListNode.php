<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\NodesListNode;

/**
 * Template parameters a type declares, in the order they are written in.
 *
 * The list is never empty: a type that declares no parameters carries no
 * list at all.
 *
 * ```
 *  callable<T, U of Some>(T): U
 *  //       ^  ^^^^^^^^^
 *  //       two parameters
 * ```
 *
 * @template-extends NodesListNode<TemplateParameterNode, true>
 */
final class TemplateParameterListNode extends NodesListNode
{
    /**
     * @param iterable<mixed, TemplateParameterNode> $items
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of no parameters are passed
     */
    public function __construct(iterable $items, int $offset = 0)
    {
        parent::__construct($items, $offset);

        if (\count($this->items) < 1) {
            throw new \InvalidArgumentException('Template parameters must have at least one item');
        }
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        parent::offsetUnset($offset);

        if (\count($this->items) < 1) {
            throw new \UnderflowException('Template parameters must have at least one item');
        }
    }
}
