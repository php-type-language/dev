<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NullableTypeNode;

final class NullableTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresWrappedType(): void
    {
        $inner = new NamedTypeNode(Name::createFromString('string'));
        $node = new NullableTypeNode($inner);

        $this->assertSame($inner, $node->type);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $inner = new NamedTypeNode(Name::createFromString('int'));
        $node = new NullableTypeNode($inner);

        $this->assertSame(0, $node->offset);
    }

    #[Test]
    public function wrapsAnotherNullableTypeNode(): void
    {
        $inner = new NullableTypeNode(new NamedTypeNode(Name::createFromString('bool')));
        $outer = new NullableTypeNode($inner);

        $this->assertSame($inner, $outer->type);
        $this->assertInstanceOf(NullableTypeNode::class, $outer->type);
    }
}
