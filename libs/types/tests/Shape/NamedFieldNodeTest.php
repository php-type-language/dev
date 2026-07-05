<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Tests\TestCase;

final class NamedFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = new Identifier('name');
        $type = new NamedTypeNode(Name::createFromString('string'));
        $node = new NamedFieldNode($key, $type);

        $this->assertSame($key, $node->key);
        $this->assertSame($type, $node->type);
    }

    #[Test]
    public function indexReturnsKeyValue(): void
    {
        $key = new Identifier('myField');
        $node = new NamedFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame('myField', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new NamedFieldNode(
            new Identifier('field'),
            new NamedTypeNode(Name::createFromString('string')),
        );

        $this->assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new NamedFieldNode(
            new Identifier('field'),
            new NamedTypeNode(Name::createFromString('string')),
            true,
        );

        $this->assertTrue($node->isOptional);
    }

    #[Test]
    public function toStringReturnsRequiredWhenNotOptional(): void
    {
        $node = new NamedFieldNode(
            new Identifier('field'),
            new NamedTypeNode(Name::createFromString('string')),
        );

        $this->assertSame('required', (string) $node);
    }

    #[Test]
    public function toStringReturnsOptionalWhenOptional(): void
    {
        $node = new NamedFieldNode(
            new Identifier('field'),
            new NamedTypeNode(Name::createFromString('string')),
            true,
        );

        $this->assertSame('optional', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NamedFieldNode(
            new Identifier('f'),
            new NamedTypeNode(Name::createFromString('int')),
        );

        $this->assertSame(0, $node->offset);
    }
}
