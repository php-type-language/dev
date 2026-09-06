<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

/**
 * Fetches sequences by a hand written scanner: Each escaped char is visited
 * only once, so the string is not traversed as a whole.
 */
final class ScannerSequenceFetcher implements SequenceFetcherInterface
{
    /**
     * @var non-empty-string
     */
    private const HEX_DIGITS = '0123456789abcdefABCDEF';

    /**
     * @var non-empty-string
     */
    private const OCTAL_DIGITS = '01234567';

    public static function fetch(string $body): array
    {
        $result = self::ESCAPED_CHARS;
        $length = \strlen($body);
        $offset = 0;

        while ($offset < $length) {
            $position = \strpos($body, '\\', $offset);

            if ($position === false) {
                break;
            }

            $offset = $position + 1;
            $char = $body[$offset] ?? '';

            // An escaped backslash consumes the char behind it
            if ($char === '\\') {
                ++$offset;

                continue;
            }

            // A hexadecimal sequence, like a "\xFF"
            if ($char === 'x' || $char === 'X') {
                $digits = \strspn($body, self::HEX_DIGITS, $offset + 1, 2);

                if ($digits !== 0) {
                    $sequence = \substr($body, $position, $digits + 2);
                    // @phpstan-ignore-next-line : A hexdec returns int<0, 255>
                    $result[$sequence] ??= \chr((int) \hexdec(\substr($sequence, 2)));
                    $offset += $digits + 1;

                    continue;
                }
            } elseif ($char === 'u') {
                $digits = \strspn($body, self::HEX_DIGITS, $offset + 2);

                // An utf-8 sequence, like a "\u{FFFF}"
                if (
                    $digits !== 0
                    && ($body[$offset + 1] ?? '') === '{'
                    && ($body[$offset + $digits + 2] ?? '') === '}'
                ) {
                    $sequence = \substr($body, $position, $digits + 4);

                    /** @var int<0, max> $code */
                    $code = (int) \hexdec(\substr($sequence, 3, $digits));

                    $result[$sequence] ??= UtfCharRenderer::render($code);
                    $offset += $digits + 3;

                    continue;
                }
            } elseif ($char >= '0' && $char <= '7') {
                // An octal sequence, like a "\101". Overflowed sequences
                // (greater than a "\377") are truncated, like in PHP itself.
                $digits = \strspn($body, self::OCTAL_DIGITS, $offset, 3);
                $sequence = \substr($body, $position, $digits + 1);
                $result[$sequence] ??= \chr(((int) \octdec(\substr($sequence, 1))) & 0xFF);
                $offset += $digits;

                continue;
            }

            ++$offset;
        }

        return $result;
    }
}
