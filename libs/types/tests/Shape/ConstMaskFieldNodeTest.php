<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ConstMaskFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ConstMaskFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = new ConstMaskNode(Name::createFromString('SOME_CONST'));
        $type = new NamedTypeNode(Name::createFromString('int'));
        $node = new ConstMaskFieldNode($key, $type);

        self::assertSame($key, $node->key);
        self::assertSame($type, $node->type);
    }

    #[Test]
    public function indexReturnsConstMaskNodeAsString(): void
    {
        $key = new ConstMaskNode(Name::createFromString('MY_CONST'));
        $node = new ConstMaskFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('MY_CONST*', $node->index);
    }

    #[Test]
    public function indexWithQualifiedName(): void
    {
        $key = new ConstMaskNode(Name::createFromString('Vendor\Pkg\STATUS'));
        $node = new ConstMaskFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('Vendor\Pkg\STATUS*', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new ConstMaskFieldNode(
            new ConstMaskNode(Name::createFromString('FOO')),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new ConstMaskFieldNode(
            new ConstMaskNode(Name::createFromString('FOO')),
            new NamedTypeNode(Name::createFromString('int')),
            true,
        );

        self::assertTrue($node->isOptional);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ConstMaskFieldNode(
            new ConstMaskNode(Name::createFromString('X')),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertSame(0, $node->offset);
    }
}
