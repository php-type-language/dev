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

        self::assertSame('foo', $node->value);
        self::assertSame('$foo', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new VariableLiteralNode('$bar');

        self::assertSame('$bar', (string) $node);
    }

    #[Test]
    public function parseWithoutDollarSignAddsDollar(): void
    {
        $node = VariableLiteralNode::parse('myVar');

        self::assertSame('myVar', $node->value);
        self::assertSame('$myVar', $node->raw);
    }

    #[Test]
    public function parseWithDollarSignKeepsValue(): void
    {
        $node = VariableLiteralNode::parse('$myVar');

        self::assertSame('myVar', $node->value);
        self::assertSame('$myVar', $node->raw);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new VariableLiteralNode('$x');

        self::assertSame(0, $node->offset);
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
