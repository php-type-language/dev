<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ImplicitFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresType(): void
    {
        $type = new NamedTypeNode(Name::createFromString('string'));
        $node = new ImplicitFieldNode($type);

        self::assertSame($type, $node->type);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new ImplicitFieldNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new ImplicitFieldNode(
            new NamedTypeNode(Name::createFromString('int')),
            true,
        );

        self::assertTrue($node->isOptional);
    }

    #[Test]
    public function toStringReturnsRequiredWhenNotOptional(): void
    {
        $node = new ImplicitFieldNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('required', (string) $node);
    }

    #[Test]
    public function toStringReturnsOptionalWhenOptional(): void
    {
        $node = new ImplicitFieldNode(
            new NamedTypeNode(Name::createFromString('int')),
            true,
        );

        self::assertSame('optional', (string) $node);
    }

    #[Test]
    public function attributesDefaultToNull(): void
    {
        $node = new ImplicitFieldNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertNull($node->attributes);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ImplicitFieldNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertSame(0, $node->offset);
    }
}
