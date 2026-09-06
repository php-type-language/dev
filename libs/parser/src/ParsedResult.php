<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Type\TypeNode;

final class ParsedResult
{
    public function __construct(
        public readonly TypeNode $type,
        /**
         * Last processed token offset.
         *
         * @var int<0, max>
         */
        public readonly int $offset,
    ) {}
}
