<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * The name of a constant written in part.
 *
 * The segments the name is made of are kept in the order they are written
 * in, with a {@see WildcardNode} standing wherever the name is left unsaid.
 *
 * Note that an empty mask node list CANNOT be created, and this is
 * an undefined behavior.
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
 * @template-extends NodeList<Identifier|WildcardNode, true>
 */
final class MaskNode extends NodeList implements \Stringable
{
    /**
     * @param iterable<mixed, Identifier|WildcardNode> $items
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
        parent::offsetUnset($offset);

        if (\count($this->items) < 1) {
            throw new \UnderflowException('A mask must have at least one item');
        }
    }

    /**
     * Gets the literal segments of a mask, in the order they are written in,
     * leaving every wildcard out.
     *
     * ```
     *  // Some\Any::BAR*BAZ*SOME
     *  $mask->getSegments(); // [BAR, BAZ, SOME]
     *
     *  // Some\Any::*
     *  $mask->getSegments(); // []
     * ```
     *
     * @return list<Identifier>
     */
    public function getSegments(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            if ($item instanceof Identifier) {
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
            if ($item instanceof Identifier) {
                $result[] = $item->value;
            }
        }

        return $result;
    }

    /**
     * Returns a mask as the string it is written as.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        $result = '';

        foreach ($this->items as $item) {
            $result .= $item->toString();
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
