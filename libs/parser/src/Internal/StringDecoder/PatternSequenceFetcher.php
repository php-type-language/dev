<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\StringDecoder;

/**
 * Fetches sequences by a single regexp pass over the whole string.
 */
final class PatternSequenceFetcher implements SequenceFetcherInterface
{
    /**
     * @var non-empty-string
     */
    private const NUMERIC_SEQUENCE_PATTERN = '/\\\\(?:u\{([0-9a-fA-F]+)\}|[xX]([0-9a-fA-F]{1,2})|([0-7]{1,3}))/';

    public static function fetch(string $body): array
    {
        $count = @\preg_match_all(self::NUMERIC_SEQUENCE_PATTERN, $body, $matches, \PREG_SET_ORDER);

        if ($count === false || $count === 0) {
            return self::ESCAPED_CHARS;
        }

        $result = self::ESCAPED_CHARS;

        /** @var list<array{0: non-empty-string, 1: string, 2?: string, 3?: string}> $matches */
        foreach ($matches as $match) {
            if (isset($result[$match[0]])) {
                continue;
            }

            // A unicode sequence, like a "\u{FFFF}"
            if ($match[1] !== '') {
                /** @var int<0, max> $code */
                $code = (int) \hexdec($match[1]);

                $result[$match[0]] = UtfCharRenderer::render($code);

                continue;
            }

            // A hexadecimal sequence, like a "\xFF"
            if (($match[2] ?? '') !== '') {
                // @phpstan-ignore-next-line : A hexdec returns int<0, 255>
                $result[$match[0]] = \chr((int) \hexdec($match[2] ?? ''));

                continue;
            }

            // An octal sequence, like a "\101". Overflowed sequences
            // (greater than a "\377") are truncated, like in PHP itself.
            $result[$match[0]] = \chr(((int) \octdec($match[3] ?? '')) & 0xFF);
        }

        return $result;
    }
}
