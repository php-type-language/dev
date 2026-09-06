<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\StringDecoder;

interface SequenceFetcherInterface
{
    /**
     * Sequences with a constant replacement.
     *
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    public const ESCAPED_CHARS = [
        '\n' => "\n",
        '\r' => "\r",
        '\t' => "\t",
        '\v' => "\v",
        '\e' => "\e",
        '\f' => "\f",
        '\$' => '$',
        '\\"' => '"',
        '\\\\' => '\\',
    ];

    /**
     * Returns a "sequence => replacement" map of all constant sequences along
     * with each hexadecimal (like a "\xFF"), octal (like a "\101") and utf-8
     * (like a "\u{FFFF}") sequence occurred in the passed string.
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     *
     * @return non-empty-array<string, string>
     */
    public static function fetch(string $body): array;
}
