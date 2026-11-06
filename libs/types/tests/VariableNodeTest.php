<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

final class VariableNodeTest extends TestCase
{

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

    #[Test]
    public function fromBuildsTheNameFromAString(): void
    {
        $node = VariableNode::from('value', 2);

        self::assertSame('value', $node->name->value);
        self::assertSame(2, $node->offset);
    }

    /**
     * The one variable that is a type of its own is the `$this`.
     */
    #[Test]
    public function thisIsAType(): void
    {
        self::assertInstanceOf(TypeNode::class, new ThisTypeNode());
    }
}
