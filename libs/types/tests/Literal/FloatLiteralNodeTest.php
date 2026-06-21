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

        $this->assertSame(3.14, $node->value);
        $this->assertSame('3.14', $node->raw);
    }

    #[Test]
    public function parseDerivesRawFromValue(): void
    {
        $node = FloatLiteralNode::parse('1.5');

        $this->assertSame(1.5, $node->value);
        $this->assertSame('1.5', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new FloatLiteralNode(3.14, '3.14');

        $this->assertSame('3.14', (string) $node);
    }

    #[Test]
    public function parseValidFloat(): void
    {
        $node = FloatLiteralNode::parse('3.14');

        $this->assertSame(3.14, $node->value);
        $this->assertSame('3.14', $node->raw);
    }

    #[Test]
    public function parseNegativeFloat(): void
    {
        $node = FloatLiteralNode::parse('-1.5');

        $this->assertSame(-1.5, $node->value);
    }

    #[Test]
    public function parseScientificNotation(): void
    {
        $node = FloatLiteralNode::parse('1.5e2');

        $this->assertSame(150.0, $node->value);
    }

    #[Test]
    public function parseNonNumericStringReturnsZero(): void
    {
        $node = FloatLiteralNode::parse('not-a-number');

        $this->assertSame(0.0, $node->value);
        $this->assertSame('not-a-number', $node->raw);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = FloatLiteralNode::parse('0.0');

        $this->assertSame(0, $node->offset);
    }
}
