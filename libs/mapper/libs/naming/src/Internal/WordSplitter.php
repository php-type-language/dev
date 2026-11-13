<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Naming\Internal;

/**
 * Splits a name into words by case boundaries, digits and separators.
 *
 * Bytes `A-Z` are uppercase letters, bytes `a-z` and all non-ASCII bytes
 * are lowercase letters, `0-9` are digits, anything else is a separator.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Naming
 */
final class WordSplitter
{
    /**
     * Word is either an optional uppercase letter followed by lowercase
     * letters and digits (`Server`, `utf8`), or a run of uppercase letters
     * not followed by a lowercase letter with optional trailing digits
     * (`HTTP`, `HTTP2`).
     *
     * @var non-empty-string
     */
    private const PATTERN = '/[A-Z]?[a-z0-9\x80-\xff]+'
        . '|[A-Z]+(?![a-z\x80-\xff])(?:[0-9][a-z0-9\x80-\xff]*)?/';

    /**
     * Returns words of the name in their original case. The list is empty
     * when the name has no letters and digits.
     *
     * @return list<non-empty-string>
     */
    public static function split(string $name): array
    {
        \preg_match_all(self::PATTERN, $name, $matches);

        /** @var list<non-empty-string> */
        return $matches[0];
    }
}
