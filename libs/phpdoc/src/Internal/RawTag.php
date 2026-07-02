<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal;

final readonly class RawTag
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
        public string $suffix,
        /**
         * @var int<0, max>
         */
        public int $offset,
    ) {}
}
