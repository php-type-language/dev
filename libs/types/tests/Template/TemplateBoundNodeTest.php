<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

/**
 * Tests for one end a template parameter is bounded at.
 */
final class TemplateBoundNodeTest extends TestCase
{
    #[Test]
    public function boundIsANodeAndNoType(): void
    {
        $node = new TemplateBoundNode(new NamedTypeNode(new Name('int')), new Identifier('of'));

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheTypeTheOperatorAndTheOffset(): void
    {
        $type = new NamedTypeNode(new Name('int'));
        $operator = new Identifier('super');

        $node = new TemplateBoundNode($type, $operator, 4);

        self::assertSame($type, $node->type);
        self::assertSame($operator, $node->operator);
        self::assertSame(4, $node->offset);
    }
}
