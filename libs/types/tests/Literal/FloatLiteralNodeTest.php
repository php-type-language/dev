<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class FloatLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueAndRaw(): void
    {
        $node = new FloatLiteralNode(3.14, '3.14');

        self::assertSame(3.14, $node->value);
        self::assertSame('3.14', $node->raw);
    }

    #[Test]
    public function parseDerivesRawFromValue(): void
    {
        $node = FloatLiteralNode::parse('1.5');

        self::assertSame(1.5, $node->value);
        self::assertSame('1.5', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new FloatLiteralNode(3.14, '3.14');

        self::assertSame('3.14', (string) $node);
    }

    #[Test]
    public function parseValidFloat(): void
    {
        $node = FloatLiteralNode::parse('3.14');

        self::assertSame(3.14, $node->value);
        self::assertSame('3.14', $node->raw);
    }

    #[Test]
    public function parseNegativeFloat(): void
    {
        $node = FloatLiteralNode::parse('-1.5');

        self::assertSame(-1.5, $node->value);
    }

    #[Test]
    public function parseScientificNotation(): void
    {
        $node = FloatLiteralNode::parse('1.5e2');

        self::assertSame(150.0, $node->value);
    }

    #[Test]
    public function parseNonNumericStringReturnsZero(): void
    {
        $node = FloatLiteralNode::parse('not-a-number');

        self::assertSame(0.0, $node->value);
        self::assertSame('not-a-number', $node->raw);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = FloatLiteralNode::parse('0.0');

        self::assertSame(0, $node->offset);
    }
}
