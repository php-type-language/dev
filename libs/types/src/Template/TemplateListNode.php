<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\NodesListNode;

/**
 * Template arguments a type is referenced with, in the order they are
 * written in. A slot of the list may also declare a parameter in place of
 * passing an argument.
 *
 * The list is never empty.
 *
 * ```
 *  Some\Any<int, string>
 *  //       ^^^  ^^^^^^ two arguments
 * ```
 *
 * @template-extends NodesListNode<TemplateNode, true>
 */
final class TemplateListNode extends NodesListNode
{
    /**
     * @param iterable<mixed, TemplateNode> $items
     * @param int<0, max> $offset
     */
    public function __construct(iterable $items, int $offset = 0)
    {
        parent::__construct($items, $offset);

        if (\count($this->items) < 1) {
            throw new \InvalidArgumentException('Template arguments must have at least one item');
        }
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        parent::offsetUnset($offset);

        if (\count($this->items) < 1) {
            throw new \UnderflowException('Template arguments must have at least one item');
        }
    }
}
