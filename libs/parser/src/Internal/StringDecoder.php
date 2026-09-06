<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use TypeLang\Parser\Internal\StringDecoder\PatternSequenceFetcher;
use TypeLang\Parser\Internal\StringDecoder\ScannerSequenceFetcher;
use TypeLang\Parser\Internal\StringDecoder\SequenceFetcherInterface;

final class StringDecoder
{
    /**
     * @var non-empty-string
     */
    private const NUMERIC_PREFIX_PATTERN = '/\\\\[uxX0-7]/';

    /**
     * Scanning a string by hand is cheaper than a regexp while it contains
     * only a few escaped chars, but loses on the escape dense ones.
     *
     * @var int<0, max>
     */
    private const SCANNER_THRESHOLD = 4;

    public static function unescape(string $value): string
    {
        return \strtr($value, ["\'" => "'", '\\\\' => '\\']);
    }

    /**
     * Method for parsing and decode all escaped character sequences: Special
     * chars (like a "\n"), hexadecimal (like a "\xFF"), octal (like a "\101")
     * and utf-8 (like a "\u{FFFF}") ones.
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     */
    public static function decode(string $body): string
    {
        if (!\str_contains($body, '\\')) {
            return $body;
        }

        return \strtr($body, self::fetchReplacements($body));
    }

    /**
     * Chooses a {@see SequenceFetcherInterface} implementation suitable for
     * the passed string.
     *
     * @return non-empty-array<string, string>
     */
    private static function fetchReplacements(string $body): array
    {
        // Numeric sequences require an additional pass, which can be skipped
        // in case of the string does not contain any of their prefixes.
        if (@\preg_match(self::NUMERIC_PREFIX_PATTERN, $body) !== 1) {
            return SequenceFetcherInterface::ESCAPED_CHARS;
        }

        if (\substr_count($body, '\\') <= self::SCANNER_THRESHOLD) {
            return ScannerSequenceFetcher::fetch($body);
        }

        return PatternSequenceFetcher::fetch($body);
    }
}
