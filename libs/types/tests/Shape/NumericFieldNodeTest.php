<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\NumericFieldNode;
use TypeLang\Type\Tests\TestCase;

final class NumericFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = IntLiteralNode::parse('0');
        $type = new NamedTypeNode(Name::createFromString('string'));
        $node = new NumericFieldNode($key, $type);

        self::assertSame($key, $node->key);
        self::assertSame($type, $node->type);
    }

    #[Test]
    public function indexReturnsStringifiedKeyValue(): void
    {
        $key = IntLiteralNode::parse('42');
        $node = new NumericFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('42', $node->index);
    }

    #[Test]
    public function indexForZeroKey(): void
    {
        $key = IntLiteralNode::parse('0');
        $node = new NumericFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('0', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new NumericFieldNode(
            IntLiteralNode::parse('1'),
            new NamedTypeNode(Name::createFromString('string')),
        );

        self::assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new NumericFieldNode(
            IntLiteralNode::parse('1'),
            new NamedTypeNode(Name::createFromString('string')),
            true,
        );

        self::assertTrue($node->isOptional);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NumericFieldNode(
            IntLiteralNode::parse('0'),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertSame(0, $node->offset);
    }
}
