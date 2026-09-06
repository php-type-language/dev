<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Literal\IntLiteralParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralNode;

final class IntLiteralParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, non-empty-string}>
     */
    public static function provideIntegers(): iterable
    {
        yield 'zero' => ['0', 0, '0'];
        yield 'decimal' => ['42', 42, '42'];
        yield 'negative decimal' => ['-42', -42, '-42'];
        yield 'hexadecimal' => ['0x1F', 31, '31'];
        yield 'uppercase hexadecimal prefix' => ['0X1F', 31, '31'];
        yield 'negative hexadecimal' => ['-0x10', -16, '-16'];
        yield 'binary' => ['0b1010', 10, '10'];
        yield 'uppercase binary prefix' => ['0B1010', 10, '10'];
        yield 'octal' => ['0o17', 15, '15'];
        yield 'uppercase octal prefix' => ['0O17', 15, '15'];
        yield 'legacy octal' => ['017', 15, '15'];
        yield 'underscored' => ['1_000_000', 1000000, '1000000'];
        yield 'underscored hexadecimal' => ['0xFF_FF', 65535, '65535'];
    }

    #[Test]
    #[DataProvider('provideIntegers')]
    public function integerIsParsedToItsDecimalValue(string $literal, int $value, string $decimal): void
    {
        $node = IntLiteralParser::parse($literal);

        self::assertSame($value, $node->value);
        self::assertSame($decimal, $node->decimal);
    }

    #[Test]
    #[DataProvider('provideIntegers')]
    public function integerParsingKeepsTheOriginalRepresentation(string $literal, int $value, string $decimal): void
    {
        self::assertSame($literal, IntLiteralParser::parse($literal)->raw);
    }

    #[Test]
    public function integerParsingSupportsPhpIntMin(): void
    {
        $node = IntLiteralParser::parse((string) \PHP_INT_MIN);

        self::assertSame(\PHP_INT_MIN, $node->value);
    }

    #[Test]
    public function integerParsingSupportsPhpIntMax(): void
    {
        $node = IntLiteralParser::parse((string) \PHP_INT_MAX);

        self::assertSame(\PHP_INT_MAX, $node->value);
    }

    #[Test]
    public function negativeZeroIsParsedAsZero(): void
    {
        $node = IntLiteralParser::parse('-0');

        self::assertSame(0, $node->value);
    }
}
