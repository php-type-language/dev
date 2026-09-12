<?php

declare(strict_types=1);

namespace TypeLang\Bench\Parser\StringDecoding;

abstract class StringDecodingBench
{
    /**
     * @var non-empty-string
     */
    private const LOREM = 'lorem ipsum dolor sit amet ';

    /**
     * @var int<1, max>
     */
    private const REPEATS = 16;

    /**
     * @return iterable<non-empty-string, array{string: non-empty-string, double: bool}>
     */
    public static function stringsDataProvider(): iterable
    {
        yield 'plain' => ['string' => '"example"', 'double' => true];
        yield 'plain long' => ['string' => '"' . \str_repeat(self::LOREM, 8) . '"', 'double' => true];
        yield 'quote' => ['string' => '"a\"b"', 'double' => true];
        yield 'few specials' => ['string' => '"line\nand\ttab"', 'double' => true];
        yield 'many specials' => ['string' => '"' . \str_repeat('a\nb\tc\r', self::REPEATS) . '"', 'double' => true];
        yield 'many backslashes' => ['string' => '"' . \str_repeat('a\\\\b', self::REPEATS) . '"', 'double' => true];
        yield 'one hexadecimal' => ['string' => '"a\x41b"', 'double' => true];
        yield 'numeric mix' => ['string' => '"\x41\101\u{1F600}\x42"', 'double' => true];
        yield 'many numeric' => ['string' => '"' . \str_repeat('\x41\101\u{48}', self::REPEATS) . '"', 'double' => true];
        yield 'single quoted plain' => ['string' => "'" . \str_repeat(self::LOREM, 8) . "'", 'double' => false];
        yield 'single quoted escaped' => ['string' => "'" . \str_repeat('a\\\\b\\\'c', self::REPEATS) . "'", 'double' => false];
    }

    /**
     * @param array{string: non-empty-string, double: bool} $params
     */
    abstract public function benchDecodeString(array $params): void;
}
