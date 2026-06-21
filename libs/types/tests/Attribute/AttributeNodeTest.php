<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Attribute;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeArgumentListNode;
use TypeLang\Type\Attribute\AttributeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Tests\TestCase;

final class AttributeNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $name = Name::createFromString('Deprecated');
        $node = new AttributeNode($name);

        $this->assertSame($name, $node->name);
    }

    #[Test]
    public function argumentsDefaultToNull(): void
    {
        $node = new AttributeNode(Name::createFromString('Pure'));

        $this->assertNull($node->arguments);
    }

    #[Test]
    public function constructorStoresArguments(): void
    {
        $args = new AttributeArgumentListNode();
        $node = new AttributeNode(Name::createFromString('Attr'), $args);

        $this->assertSame($args, $node->arguments);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new AttributeNode(Name::createFromString('Attr'));

        $this->assertSame(0, $node->offset);
    }
}
