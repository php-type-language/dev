<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path;

use TypeLang\Mapper\Diagnostics\Path\Segment\SegmentInterface;

/**
 * Location of a value inside the mapped input and the type it is mapped by,
 * from the root.
 *
 * @template-implements \IteratorAggregate<int<0, max>, SegmentInterface>
 */
final class Path implements \IteratorAggregate, \Countable
{
    private static ?self $empty = null;

    public readonly bool $isEmpty;

    public function __construct(
        /**
         * Segments from the root, an empty list is the root itself
         *
         * @var list<SegmentInterface>
         */
        public readonly array $segments = [],
    ) {
        $this->isEmpty = \count($this->segments) === 0;
    }

    public static function empty(): self
    {
        return self::$empty ??= new self();
    }

    public function toArray(): array
    {
        return $this->segments;
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->segments);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->segments);
    }
}
