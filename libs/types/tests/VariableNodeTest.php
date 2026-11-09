<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
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
        $node = new VariableNode('foo');

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheNameAndTheOffset(): void
    {
        $node = new VariableNode('value', 2);

        self::assertSame('value', $node->name);
        self::assertSame(2, $node->offset);
    }

    #[Test]
    public function emptyNameIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VariableNode('');
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
