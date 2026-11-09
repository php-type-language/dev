<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateBoundNode\BoundKind;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

/**
 * Tests for a template parameter declared by a type.
 */
final class TemplateParameterNodeTest extends TestCase
{
    private static function type(): TypeNode
    {
        return new NamedTypeNode(new Name('int'));
    }

    #[Test]
    public function parameterWithNothingButTheName(): void
    {
        $node = new TemplateParameterNode(new Identifier('T'));

        self::assertSame('T', $node->name->value);
        self::assertNull($node->upper);
        self::assertNull($node->lower);
        self::assertNull($node->default);
        self::assertNull($node->variance);
        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function constructorKeepsEveryPartOfTheParameter(): void
    {
        $upper = new TemplateBoundNode(self::type(), BoundKind::Upper);
        $lower = new TemplateBoundNode(self::type(), BoundKind::Lower);
        $default = self::type();
        $variance = new VarianceNode(VarianceKind::Covariant);

        $node = new TemplateParameterNode(new Identifier('T'), $upper, $lower, $default, $variance, 5);

        self::assertSame($upper, $node->upper);
        self::assertSame($lower, $node->lower);
        self::assertSame($default, $node->default);
        self::assertSame($variance, $node->variance);
        self::assertSame(5, $node->offset);
    }

    #[Test]
    public function lowerBoundIsNotAnUpperOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TemplateParameterNode(
            new Identifier('T'),
            new TemplateBoundNode(self::type(), BoundKind::Lower),
        );
    }

    #[Test]
    public function upperBoundIsNotALowerOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TemplateParameterNode(
            new Identifier('T'),
            null,
            new TemplateBoundNode(self::type(), BoundKind::Upper),
        );
    }
}
