<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypesListNode;

final class TypesListNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresWrappedType(): void
    {
        $inner = new NamedTypeNode(Name::createFromString('string'));
        $node = new TypesListNode($inner);

        $this->assertSame($inner, $node->type);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new TypesListNode(new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame(0, $node->offset);
    }

    #[Test]
    public function wrapsAnotherTypesListNode(): void
    {
        $inner = new TypesListNode(new NamedTypeNode(Name::createFromString('int')));
        $outer = new TypesListNode($inner);

        $this->assertSame($inner, $outer->type);
    }
}
