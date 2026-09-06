<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class VariableLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresNameWithoutDollarSign(): void
    {
        $node = new VariableLiteralNode('foo');

        self::assertSame('foo', $node->value);
    }

    #[Test]
    public function rawValueIsPrefixedByDollarSign(): void
    {
        $node = new VariableLiteralNode('foo');

        self::assertSame('$foo', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new VariableLiteralNode('bar');

        self::assertSame('$bar', (string) $node);
    }

    #[Test]
    public function singleCharacterVariableIsAllowed(): void
    {
        $node = new VariableLiteralNode('a');

        self::assertSame('a', $node->value);
        self::assertSame('$a', $node->raw);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new VariableLiteralNode('x');

        self::assertSame(0, $node->offset);
    }
}
