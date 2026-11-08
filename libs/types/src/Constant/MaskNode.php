<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NodesListNode;

/**
 * The name of a constant written in part.
 *
 * The segments the name is made of are kept in the order they are written
 * in, with a {@see WildcardMaskNode} standing wherever the name is left unsaid.
 *
 * A mask is never empty.
 *
 * ```
 *  Some\Any::BAR*BAZ*SOME
 *  //           ^   ^      // wildcards
 *  //        ^^^ ^^^ ^^^^  // segments
 *  //        the segments and the wildcards between them
 *
 *  Some\Any::*
 *  //        ^ a single wildcard
 * ```
 *
 * @template-extends NodesListNode<Name|WildcardMaskNode, true>
 */
final class MaskNode extends NodesListNode implements \Stringable
{
    /**
     * @param iterable<mixed, Name|WildcardMaskNode> $items
     * @param int<0, max> $offset
     */
    public function __construct(iterable $items, int $offset = 0)
    {
        parent::__construct($items, $offset);

        if (\count($this->items) < 1) {
            throw new \InvalidArgumentException('A mask must have at least one item');
        }
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        $previous = $this->items;

        parent::offsetUnset($offset);

        if (\count($this->items) < 1) {
            $this->items = $previous;

            throw new \UnderflowException('A mask must have at least one item');
        }
    }

    /**
     * The literal segments of a mask without the wildcards.
     *
     * ```
     *  // Some\Any::BAR*BAZ*SOME
     *  $mask->getSegments(); // [BAR, BAZ, SOME]
     *
     *  // Some\Any::*
     *  $mask->getSegments(); // []
     * ```
     *
     * @return list<Name>
     */
    public function getSegments(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            if ($item instanceof Name) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getSegmentsAsStrings(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            if ($item instanceof Name) {
                $result[] = $item->unqualified;
            }
        }

        return $result;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        $result = '';

        foreach ($this->items as $item) {
            if ($item instanceof Name) {
                $result .= $item->unqualified;
            } else {
                $result .= $item->toString();
            }
        }

        /** @var non-empty-string */
        return $result;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
