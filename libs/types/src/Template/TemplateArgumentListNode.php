<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\NodeList;

/**
 * Template arguments a type is referenced with, in the order they are
 * written in.
 *
 * Note that an empty template argument list CANNOT be created, and this is
 * an undefined behavior.
 *
 * ```
 *  Some\Any<int, string>
 *  //       ^^^  ^^^^^^ two arguments
 * ```
 *
 * @template-extends NodeList<TemplateArgumentNode, true>
 */
final class TemplateArgumentListNode extends NodeList
{
    /**
     * @param iterable<mixed, TemplateArgumentNode> $items
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
