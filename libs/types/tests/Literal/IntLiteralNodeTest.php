<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class IntLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueRawDecimalAndKind(): void
    {
        $node = new IntLiteralNode(42, '42', IntLiteralKind::Hexadecimal, '0x2A');

        self::assertSame(42, $node->value);
        self::assertSame('42', $node->decimal);
        self::assertSame(IntLiteralKind::Hexadecimal, $node->kind);
        self::assertSame('0x2A', $node->raw);
    }

    #[Test]
    public function decimalIsDerivedFromValue(): void
    {
        self::assertSame('-42', (new IntLiteralNode(-42))->decimal);
    }

    #[Test]
    public function decimalOfLowerBoundIsANumber(): void
    {
        self::assertSame('-9223372036854775808', (new IntLiteralNode(\PHP_INT_MIN))->decimal);
    }

    /**
     * @return iterable<non-empty-string, array{int, IntLiteralKind, non-empty-string}>
     */
    public static function derivedRawValuesDataProvider(): iterable
    {
        yield 'decimal' => [255, IntLiteralKind::Decimal, '255'];
        yield 'negative decimal' => [-255, IntLiteralKind::Decimal, '-255'];
        yield 'lower bound in decimal' => [\PHP_INT_MIN, IntLiteralKind::Decimal, '-9223372036854775808'];

        yield 'hexadecimal' => [255, IntLiteralKind::Hexadecimal, '0xff'];
        yield 'negative hexadecimal' => [-255, IntLiteralKind::Hexadecimal, '-0xff'];
        yield 'hexadecimal zero' => [0, IntLiteralKind::Hexadecimal, '0x0'];
        yield 'upper bound in hexadecimal' => [\PHP_INT_MAX, IntLiteralKind::Hexadecimal, '0x7fffffffffffffff'];
        yield 'lower bound in hexadecimal' => [\PHP_INT_MIN, IntLiteralKind::Hexadecimal, '-0x8000000000000000'];

        yield 'binary' => [5, IntLiteralKind::Binary, '0b101'];
        yield 'negative binary' => [-5, IntLiteralKind::Binary, '-0b101'];
        yield 'lower bound in binary' => [\PHP_INT_MIN, IntLiteralKind::Binary, '-0b1' . \str_repeat('0', 63)];

        yield 'octal' => [255, IntLiteralKind::Octal, '0o377'];
        yield 'negative octal' => [-255, IntLiteralKind::Octal, '-0o377'];
        yield 'lower bound in octal' => [\PHP_INT_MIN, IntLiteralKind::Octal, '-0o1000000000000000000000'];
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('derivedRawValuesDataProvider')]
    public function derivedRawValueIsWrittenInTheRadixOfTheKind(int $value, IntLiteralKind $kind, string $raw): void
    {
        self::assertSame($raw, (new IntLiteralNode($value, kind: $kind))->raw);
    }

    #[Test]
    public function derivedRawValueOfDecimalNumberOutOfTheNativeRangeIsExact(): void
    {
        $node = new IntLiteralNode(\PHP_INT_MAX, '9223372036854775808');

        self::assertSame('9223372036854775808', $node->raw);
    }

    #[Test]
    public function nonDecimalNumberOutOfTheNativeRangeRequiresRawText(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new IntLiteralNode(\PHP_INT_MAX, '18446744073709551615', IntLiteralKind::Hexadecimal);
    }

    #[Test]
    public function nonDecimalNumberOutOfTheNativeRangeIsAllowedWithRawText(): void
    {
        $node = new IntLiteralNode(\PHP_INT_MAX, '18446744073709551615', IntLiteralKind::Hexadecimal, '0xFFFFFFFFFFFFFFFF');

        self::assertSame('0xFFFFFFFFFFFFFFFF', $node->raw);
    }
}
