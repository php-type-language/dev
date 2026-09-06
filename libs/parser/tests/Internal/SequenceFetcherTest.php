<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\StringDecoder;
use TypeLang\Parser\Internal\StringDecoder\PatternSequenceFetcher;
use TypeLang\Parser\Internal\StringDecoder\ScannerSequenceFetcher;
use TypeLang\Parser\Internal\StringDecoder\SequenceFetcherInterface;
use TypeLang\Parser\Tests\TestCase;

final class SequenceFetcherTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{class-string<SequenceFetcherInterface>}>
     */
    public static function provideFetchers(): iterable
    {
        yield 'scanner' => [ScannerSequenceFetcher::class];
        yield 'pattern' => [PatternSequenceFetcher::class];
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function provideBodies(): iterable
    {
        yield 'empty' => [''];
        yield 'without sequences' => ['example'];
        yield 'constant sequences' => ['a\nb\rc\td\ve\ff\$g\"h'];
        yield 'backslash' => ['a\\\\b'];
        yield 'backslash before escape sequence' => ['a\\\\nb'];
        yield 'backslash before hexadecimal' => ['a\\\\x41b'];
        yield 'hexadecimal' => ['\x41'];
        yield 'uppercase hexadecimal prefix' => ['\X41'];
        yield 'short hexadecimal' => ['\x9'];
        yield 'null byte' => ['\x00'];
        yield 'octal' => ['\101'];
        yield 'octal null byte' => ['\0'];
        yield 'octal overflow' => ['\777'];
        yield 'unicode' => ['\u{48}'];
        yield 'multibyte unicode' => ['\u{1F600}'];
        yield 'last unicode code point' => ['\u{10FFFF}'];
        yield 'code point above the unicode range' => ['\u{110000}'];
        yield 'unknown sequence' => ['a\qb'];
        yield 'incomplete unicode' => ['a\u{}b'];
        yield 'incomplete hexadecimal' => ['a\xzb'];
        yield 'trailing backslash' => ['a\\'];
        yield 'repeated sequences' => ['\x41\x41\u{42}\u{42}\101\101'];
        yield 'many sequences' => ['\x41\x42\u{43}\104\x45\u{46}\107\x48'];
        yield 'raw null byte' => ["a\0b"];
        yield 'invalid utf8' => ["\xFF\xFE"];
        yield 'all kinds at once' => ['\x41\u{42}\103\n\\\\'];
    }

    #[Test]
    #[DataProvider('provideFetchers')]
    public function fetcherImplementsTheContract(string $fetcher): void
    {
        self::assertTrue(\is_subclass_of($fetcher, SequenceFetcherInterface::class));
    }

    #[Test]
    #[DataProvider('provideBodies')]
    public function everyScannedSequenceIsAlsoMatchedByThePattern(string $body): void
    {
        $pattern = PatternSequenceFetcher::fetch($body);

        foreach (ScannerSequenceFetcher::fetch($body) as $sequence => $replacement) {
            self::assertSame($replacement, $pattern[$sequence] ?? null, \sprintf(
                'A "%s" sequence replacement must be the same in both fetchers',
                $sequence,
            ));
        }
    }

    /**
     * The scanner skips an escaped backslash together with the char behind it,
     * so a sequence that is not applied does not get into the map at all. The
     * pattern based one collects it, but {@see strtr()} never applies it: The
     * "\\" replacement wins on that position because of being matched first.
     */
    #[Test]
    public function scannerSkipsSequencesBehindAnEscapedBackslash(): void
    {
        $body = 'a\\\\x41b';

        self::assertArrayNotHasKey('\x41', ScannerSequenceFetcher::fetch($body));
        self::assertArrayHasKey('\x41', PatternSequenceFetcher::fetch($body));

        self::assertSame('a\x41b', StringDecoder::decode($body));
    }

    #[Test]
    #[DataProvider('provideBodies')]
    public function bothFetchersDecodeTheBodyIdentically(string $body): void
    {
        $scanner = \strtr($body, ScannerSequenceFetcher::fetch($body));
        $pattern = \strtr($body, PatternSequenceFetcher::fetch($body));

        self::assertSame($scanner, $pattern);
        self::assertSame($scanner, StringDecoder::decode($body));
    }

    #[Test]
    #[DataProvider('provideBodies')]
    public function scannerAlwaysContainsConstantSequences(string $body): void
    {
        $result = ScannerSequenceFetcher::fetch($body);

        foreach (SequenceFetcherInterface::ESCAPED_CHARS as $sequence => $replacement) {
            self::assertSame($replacement, $result[$sequence] ?? null);
        }
    }

    #[Test]
    #[DataProvider('provideBodies')]
    public function patternAlwaysContainsConstantSequences(string $body): void
    {
        $result = PatternSequenceFetcher::fetch($body);

        foreach (SequenceFetcherInterface::ESCAPED_CHARS as $sequence => $replacement) {
            self::assertSame($replacement, $result[$sequence] ?? null);
        }
    }

    #[Test]
    public function scannerFetchesNumericSequences(): void
    {
        $result = ScannerSequenceFetcher::fetch('\x41\u{42}\103');

        self::assertSame('A', $result['\x41'] ?? null);
        self::assertSame('B', $result['\u{42}'] ?? null);
        self::assertSame('C', $result['\103'] ?? null);
    }

    #[Test]
    public function patternFetchesNumericSequences(): void
    {
        $result = PatternSequenceFetcher::fetch('\x41\u{42}\103');

        self::assertSame('A', $result['\x41'] ?? null);
        self::assertSame('B', $result['\u{42}'] ?? null);
        self::assertSame('C', $result['\103'] ?? null);
    }

    #[Test]
    #[DataProvider('provideFetchers')]
    public function fetcherReturnsOnlyConstantSequencesForAPlainString(string $fetcher): void
    {
        self::assertSame(SequenceFetcherInterface::ESCAPED_CHARS, $fetcher::fetch('example'));
    }

    #[Test]
    public function escapedCharsContainTheBackslashItself(): void
    {
        self::assertSame('\\', SequenceFetcherInterface::ESCAPED_CHARS['\\\\'] ?? null);
    }
}
