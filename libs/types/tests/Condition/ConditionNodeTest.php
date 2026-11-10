<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Condition;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

/**
 * Tests for the question a ternary type asks.
 */
final class ConditionNodeTest extends TestCase
{
    #[Test]
    public function conditionIsANodeAndNoType(): void
    {
        $node = new ConditionNode(new VariableNode('a'), new NamedTypeNode(new Name('int')), ConditionKind::Equal);

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheSidesTheKindAndTheOffset(): void
    {
        $subject = new NamedTypeNode(new Name('T'));
        $target = new NamedTypeNode(new Name('int'));

        $node = new ConditionNode($subject, $target, ConditionKind::NotEqual, 3);

        self::assertSame($subject, $node->subject);
        self::assertSame($target, $node->target);
        self::assertSame(ConditionKind::NotEqual, $node->kind);
        self::assertSame(3, $node->offset);
    }

    #[Test]
    public function thereAreTwoKindsAndEachOneIsNamedByItsOperator(): void
    {
        self::assertSame([ConditionKind::Equal, ConditionKind::NotEqual], ConditionKind::cases());
        self::assertSame('is', ConditionKind::Equal->value);
        self::assertSame('is not', ConditionKind::NotEqual->value);
    }
}
