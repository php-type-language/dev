<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics;

use TypeLang\Mapper\Diagnostics\Path\Path;

/**
 * Single failure of mapping at a known location of the input.
 */
final class Violation
{
    public function __construct(
        public readonly ViolationCode $code,
        /**
         * Values of the template placeholders
         *
         * @var array<non-empty-string, string|\Stringable>
         */
        public readonly array $parameters,
        public readonly Path $path,
        /**
         * Rejected value, {@see null} for a missing field
         */
        public readonly mixed $value = null,
    ) {}
}
