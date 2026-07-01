<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal\Splitter;

final readonly class Segment
{
    public function __construct(
        public string $text = '',
        /**
         * @var int<0, max>
         */
        public int $offset = 0,
    ) {}
}
