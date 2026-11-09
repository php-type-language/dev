<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateBoundNode\BoundKind;
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
        $node = new TemplateBoundNode(new NamedTypeNode(new Name('int')), BoundKind::Upper);

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheTypeTheKindAndTheOffset(): void
    {
        $type = new NamedTypeNode(new Name('int'));

        $node = new TemplateBoundNode($type, BoundKind::Lower, 4);

        self::assertSame($type, $node->type);
        self::assertSame(BoundKind::Lower, $node->kind);
        self::assertSame(4, $node->offset);
    }

    #[Test]
    public function thereAreTwoKindsAndEachOneIsNamedByItsWord(): void
    {
        self::assertSame([BoundKind::Upper, BoundKind::Lower], BoundKind::cases());
        self::assertSame('of', BoundKind::Upper->value);
        self::assertSame('super', BoundKind::Lower->value);
    }
}
