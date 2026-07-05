<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Attribute;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeArgumentNode;
use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class AttributeArgumentNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValue(): void
    {
        $value = new NamedTypeNode(Name::createFromString('string'));
        $node = new AttributeArgumentNode($value);

        self::assertSame($value, $node->value);
    }

    #[Test]
    public function attributesDefaultToNull(): void
    {
        $node = new AttributeArgumentNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertNull($node->attributes);
    }

    #[Test]
    public function constructorStoresAttributes(): void
    {
        $attrs = new AttributeGroupListNode();
        $value = new NamedTypeNode(Name::createFromString('int'));
        $node = new AttributeArgumentNode($value, $attrs);

        self::assertSame($attrs, $node->attributes);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new AttributeArgumentNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertSame(0, $node->offset);
    }
}
