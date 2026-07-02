<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal;

use TypeLang\PhpDoc\Internal\Splitter\Segment;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\PhpDoc
 */
final class SourceMap
{
    /**
     * @var array<int<0, max>, int<0, max>>
     */
    private array $mappings = [];

    /**
     * @var int<0, max>
     */
    private int $offset = 0;

    /**
     * @var int<0, max>
     */
    private int $max = 0;

    /**
     * @param int<0, max> $offset
     */
    public function addMapping(string $text, int $offset): void
    {
        $this->mappings[$this->offset] = $offset;

        $length = \strlen($text);

        $this->max = \max($this->max, $offset + $length - 1);
        $this->offset += $length;
    }

    /**
     * @return int<0, max>
     */
    public function getOriginalOffset(int $offset): int
    {
        if ($offset >= $this->offset) {
            return $this->max;
        }

        $result = 0;

        foreach ($this->mappings as $from => $to) {
            if ($from > $offset) {
                return \max(0, $result + $offset);
            }

            $result = $to - $from;
        }

        return \max(0, $result + $offset);
    }
}
