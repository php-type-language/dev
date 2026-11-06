<?php

declare(strict_types=1);

namespace TypeLang\Type;

abstract class Node
{
    public function __construct(
        /**
         * Token offset defined in the source code.
         *
         * @var int<0, max>
         */
        public readonly int $offset = 0,
    ) {}
}
