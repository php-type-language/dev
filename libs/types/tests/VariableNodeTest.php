<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
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
        $node = new VariableNode(new Name('foo'));

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheNameAndTheOffset(): void
    {
        $name = new Name('value', 2);

        $node = new VariableNode($name, 2);

        self::assertSame($name, $node->name);
        self::assertSame('value', $node->name->toString());
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
