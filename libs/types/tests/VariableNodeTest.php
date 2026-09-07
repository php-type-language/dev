<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\ThisNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

final class VariableNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresTheNameWithoutTheDollarSign(): void
    {
        $node = new VariableNode(new Identifier('foo'));

        self::assertSame('foo', $node->name->toString());
    }

    #[Test]
    public function singleCharacterNameIsAllowed(): void
    {
        $node = new VariableNode(new Identifier('a'));

        self::assertSame('a', $node->name->toString());
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new VariableNode(new Identifier('x'));

        self::assertSame(0, $node->offset);
    }

    /**
     * A variable names a place a value is kept in, so it is no type of
     * its own.
     */
    #[Test]
    public function variableIsNotAType(): void
    {
        $node = new VariableNode(new Identifier('foo'));

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    /**
     * The one variable that is a type of its own is the `$this`.
     */
    #[Test]
    public function thisIsAType(): void
    {
        self::assertInstanceOf(TypeNode::class, new ThisNode());
    }

    #[Test]
    public function thisDefaultOffsetIsZero(): void
    {
        self::assertSame(0, (new ThisNode())->offset);
    }
}
