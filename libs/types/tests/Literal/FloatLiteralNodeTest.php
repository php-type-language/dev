<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class FloatLiteralNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{float, non-empty-string}>
     */
    public static function derivedRawValuesDataProvider(): iterable
    {
        yield 'whole number' => [1.0, '1.0'];
        yield 'fraction' => [0.5, '0.5'];
        yield 'negative' => [-1.5, '-1.5'];
        yield 'exponent' => [1.0E+25, '1.0E+25'];
    }

    #[Test]
    #[DataProvider('derivedRawValuesDataProvider')]
    public function derivedRawValueIsAFloatLiteral(float $value, string $raw): void
    {
        self::assertSame($raw, (new FloatLiteralNode($value))->raw);
    }

    /**
     * @return iterable<non-empty-string, array{float}>
     */
    public static function nonFiniteValuesDataProvider(): iterable
    {
        yield 'infinity' => [\INF];
        yield 'negative infinity' => [-\INF];
        yield 'not a number' => [\NAN];
    }

    #[Test]
    #[DataProvider('nonFiniteValuesDataProvider')]
    public function nonFiniteValueRequiresRawText(float $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FloatLiteralNode($value);
    }

    #[Test]
    public function nonFiniteValueIsAllowedWithRawText(): void
    {
        $node = new FloatLiteralNode(\INF, raw: '1e999');

        self::assertSame('1e999', $node->raw);
        self::assertSame('1', $node->mantissa);
        self::assertSame('999', $node->exponent);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, numeric-string, numeric-string}>
     */
    public static function mantissaAndExponentDataProvider(): iterable
    {
        yield 'fraction' => ['1.5', '1.5', '0'];
        yield 'without an integer part' => ['.5', '.5', '0'];
        yield 'without a fraction' => ['1.', '1.', '0'];
        yield 'exponent' => ['1e3', '1', '3'];
        yield 'exponent in upper case' => ['1E3', '1', '3'];
        yield 'positive exponent' => ['1e+3', '1', '3'];
        yield 'negative exponent' => ['1e-3', '1', '-3'];
        yield 'signed mantissa' => ['+1.5', '1.5', '0'];
        yield 'negative mantissa' => ['-1.5e2', '-1.5', '2'];
        yield 'separators' => ['-1_0.5_0e-1_0', '-10.50', '-10'];
        yield 'leading zeros in the mantissa' => ['-055e45', '-55', '45'];
        yield 'leading zeros before the point' => ['00.5', '0.5', '0'];
        yield 'zero before the point' => ['0.5', '0.5', '0'];
        yield 'zeros only' => ['000', '0', '0'];
        yield 'negative zero' => ['-0.0', '-0.0', '0'];
        yield 'leading zeros in the exponent' => ['1e-007', '1', '-7'];
        yield 'zero exponent' => ['1e+00', '1', '0'];
        yield 'negative zero exponent' => ['1e-00', '1', '0'];
        yield 'exponent with trailing zeros' => ['1e-100', '1', '-100'];
        yield 'exponent out of the range of a double' => ['1e999', '1', '999'];
        yield 'derived from value' => ['1.0E+25', '1.0', '25'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $mantissa
     * @param numeric-string $exponent
     */
    #[Test]
    #[DataProvider('mantissaAndExponentDataProvider')]
    public function mantissaAndExponentAreTakenFromRawText(string $raw, string $mantissa, string $exponent): void
    {
        $node = new FloatLiteralNode(1.0, raw: $raw);

        self::assertSame($mantissa, $node->mantissa);
        self::assertSame($exponent, $node->exponent);
    }

    #[Test]
    public function mantissaAndExponentAreTakenFromValueWithNoRawText(): void
    {
        $node = new FloatLiteralNode(1.0E+25);

        self::assertSame('1.0', $node->mantissa);
        self::assertSame('25', $node->exponent);
    }

    #[Test]
    public function explicitMantissaAndExponentAreKept(): void
    {
        $node = new FloatLiteralNode(0.5, '5', '-1', '0.5');

        self::assertSame('5', $node->mantissa);
        self::assertSame('-1', $node->exponent);
    }
}
