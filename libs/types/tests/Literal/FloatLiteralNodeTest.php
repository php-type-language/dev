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
        self::assertSame('1e999', (new FloatLiteralNode(\INF, '1e999'))->raw);
    }
}
