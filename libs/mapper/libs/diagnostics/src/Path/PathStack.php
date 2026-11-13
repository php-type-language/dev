<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path;

use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\SegmentInterface;

/**
 * Path of the current position during a mapping run.
 */
final class PathStack
{
    /**
     * @var array<int<0, max>, SegmentInterface|int|string>
     */
    private array $segments = [];

    /**
     * @var int<0, max>
     */
    private int $depth = 0;

    /**
     * Appends a segment, an `int` or `string` is a key of an array.
     */
    public function enter(SegmentInterface|int|string $segment): void
    {
        $this->segments[$this->depth++] = $segment;
    }

    public function leave(): void
    {
        \assert($this->depth > 0, 'Path stack is empty');

        --$this->depth;
    }

    /**
     * Returns a copy of the current path.
     */
    public function capture(): Path
    {
        $segments = [];

        for ($i = 0; $i < $this->depth; ++$i) {
            $segment = $this->segments[$i];

            if (!$segment instanceof SegmentInterface) {
                $segment = new KeySegment($segment);
            }

            $segments[] = $segment;
        }

        return new Path($segments);
    }
}
