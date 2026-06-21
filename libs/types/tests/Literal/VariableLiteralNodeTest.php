<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class VariableLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStripsLeadingDollarSign(): void
    {
        $node = new VariableLiteralNode('$foo');

        $this->assertSame('foo', $node->value);
        $this->assertSame('$foo', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new VariableLiteralNode('$bar');

        $this->assertSame('$bar', (string) $node);
    }

    #[Test]
    public function parseWithoutDollarSignAddsDollar(): void
    {
        $node = VariableLiteralNode::parse('myVar');

        $this->assertSame('myVar', $node->value);
        $this->assertSame('$myVar', $node->raw);
    }

    #[Test]
    public function parseWithDollarSignKeepsValue(): void
    {
        $node = VariableLiteralNode::parse('$myVar');

        $this->assertSame('myVar', $node->value);
        $this->assertSame('$myVar', $node->raw);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new VariableLiteralNode('$x');

        $this->assertSame(0, $node->offset);
    }

    #[Test]
    public function constructorThrowsWhenStringTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new VariableLiteralNode('$');
    }

    #[Test]
    public function constructorThrowsWhenMissingDollarSign(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new VariableLiteralNode('foo');
    }
}
