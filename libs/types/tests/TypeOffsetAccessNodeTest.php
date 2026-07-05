<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeOffsetAccessNode;

final class TypeOffsetAccessNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresTypeAndAccess(): void
    {
        $type = new NamedTypeNode(Name::createFromString('array'));
        $access = new NamedTypeNode(Name::createFromString('string'));
        $node = new TypeOffsetAccessNode($type, $access);

        self::assertSame($type, $node->type);
        self::assertSame($access, $node->access);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new TypeOffsetAccessNode(
            new NamedTypeNode(Name::createFromString('array')),
            new NamedTypeNode(Name::createFromString('key')),
        );

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function accessPropertyIsReadonly(): void
    {
        $access = new NamedTypeNode(Name::createFromString('string'));
        $node = new TypeOffsetAccessNode(
            new NamedTypeNode(Name::createFromString('array')),
            $access,
        );

        self::assertSame($access, $node->access);
    }
}
