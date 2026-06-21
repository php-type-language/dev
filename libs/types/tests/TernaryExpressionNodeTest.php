<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TernaryExpressionNode;

final class TernaryExpressionNodeTest extends TestCase
{
    private function named(string $name): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    #[Test]
    public function constructorStoresAllParts(): void
    {
        $subject = $this->named('T');
        $target = $this->named('string');
        $condition = new EqualConditionNode($subject, $target);
        $then = $this->named('true');
        $else = $this->named('false');

        $node = new TernaryExpressionNode($condition, $then, $else);

        $this->assertSame($condition, $node->condition);
        $this->assertSame($then, $node->then);
        $this->assertSame($else, $node->else);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new TernaryExpressionNode(
            new EqualConditionNode($this->named('T'), $this->named('string')),
            $this->named('A'),
            $this->named('B'),
        );

        $this->assertSame(0, $node->offset);
    }
}
