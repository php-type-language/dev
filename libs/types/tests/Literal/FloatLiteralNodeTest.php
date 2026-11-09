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

        new FloatLiteralNode($value, '1', '999');
    }

    #[Test]
    #[DataProvider('nonFiniteValuesDataProvider')]
    public function nonFiniteValueRequiresMantissaAndExponent(float $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FloatLiteralNode($value, raw: '1e999');
    }

    #[Test]
    public function nonFiniteValueIsAllowedWithRawTextMantissaAndExponent(): void
    {
        $node = new FloatLiteralNode(\INF, '1', '999', '1e999');

        self::assertSame('1e999', $node->raw);
        self::assertSame('1', $node->mantissa);
        self::assertSame('999', $node->exponent);
    }

    /**
     * @return iterable<non-empty-string, array{float, numeric-string, numeric-string}>
     */
    public static function mantissaAndExponentOfValueDataProvider(): iterable
    {
        yield 'fraction' => [1.5, '1.5', '0'];
        yield 'whole number' => [100.0, '100.0', '0'];
        yield 'negative' => [-1.5, '-1.5', '0'];
        yield 'negative zero' => [-0.0, '-0.0', '0'];
        yield 'large' => [1.0E+25, '1.0', '25'];
        yield 'small' => [1.0E-7, '1.0', '-7'];
        yield 'negative small' => [-1.5E-10, '-1.5', '-10'];
        yield 'maximum' => [\PHP_FLOAT_MAX, '1.7976931348623157', '308'];
    }

    /**
     * @param numeric-string $mantissa
     * @param numeric-string $exponent
     */
    #[Test]
    #[DataProvider('mantissaAndExponentOfValueDataProvider')]
    public function mantissaAndExponentAreTakenFromValueByDefault(float $value, string $mantissa, string $exponent): void
    {
        $node = new FloatLiteralNode($value);

        self::assertSame($mantissa, $node->mantissa);
        self::assertSame($exponent, $node->exponent);
    }

    #[Test]
    public function rawTextIsNotReadForMantissaAndExponent(): void
    {
        $node = new FloatLiteralNode(1.5, raw: '15e-1');

        self::assertSame('15e-1', $node->raw);
        self::assertSame('1.5', $node->mantissa);
        self::assertSame('0', $node->exponent);
    }

    #[Test]
    public function explicitMantissaAndExponentAreKept(): void
    {
        $node = new FloatLiteralNode(0.5, '5', '-1', '0.5');

        self::assertSame('5', $node->mantissa);
        self::assertSame('-1', $node->exponent);
    }

    #[Test]
    public function explicitMantissaIsKeptAndExponentIsTakenFromValue(): void
    {
        $node = new FloatLiteralNode(1.0E+25, '10', null, '10e24');

        self::assertSame('10', $node->mantissa);
        self::assertSame('25', $node->exponent);
    }
}
