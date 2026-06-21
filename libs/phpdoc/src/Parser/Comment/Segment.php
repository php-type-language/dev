<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Comment;

final class Segment
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public readonly string $text = '',
        public readonly int $offset = 0,
    ) {}
}
