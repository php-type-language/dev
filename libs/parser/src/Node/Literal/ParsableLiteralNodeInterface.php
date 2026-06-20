<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

interface ParsableLiteralNodeInterface
{
    /**
     * Parse raw literal string value.
     *
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    public static function parse(string $value): self;
}
