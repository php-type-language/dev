<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\StringNamedFieldNode;
use TypeLang\Type\Tests\TestCase;

final class StringNamedFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = new StringLiteralNode('my-key');
        $type = new NamedTypeNode(Name::createFromString('string'));
        $node = new StringNamedFieldNode($key, $type);

        $this->assertSame($key, $node->key);
        $this->assertSame($type, $node->type);
    }

    #[Test]
    public function indexReturnsKeyValue(): void
    {
        $key = new StringLiteralNode('field-name');
        $node = new StringNamedFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame('field-name', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new StringNamedFieldNode(
            new StringLiteralNode('key'),
            new NamedTypeNode(Name::createFromString('string')),
        );

        $this->assertFalse($node->optional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new StringNamedFieldNode(
            new StringLiteralNode('key'),
            new NamedTypeNode(Name::createFromString('string')),
            true,
        );

        $this->assertTrue($node->optional);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new StringNamedFieldNode(
            new StringLiteralNode('k'),
            new NamedTypeNode(Name::createFromString('int')),
        );

        $this->assertSame(0, $node->offset);
    }
}
