<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ClassConstMaskFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ClassConstMaskFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = new ClassConstMaskNode(Name::createFromString('MyEnum'), new Identifier('STATUS'));
        $type = new NamedTypeNode(Name::createFromString('int'));
        $node = new ClassConstMaskFieldNode($key, $type);

        $this->assertSame($key, $node->key);
        $this->assertSame($type, $node->type);
    }

    #[Test]
    public function indexWithConstantSuffix(): void
    {
        $key = new ClassConstMaskNode(Name::createFromString('MyEnum'), new Identifier('STATUS'));
        $node = new ClassConstMaskFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame('MyEnum::STATUS*', $node->index);
    }

    #[Test]
    public function indexWithNullConstant(): void
    {
        $key = new ClassConstMaskNode(Name::createFromString('MyEnum'));
        $node = new ClassConstMaskFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame('MyEnum::*', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new ClassConstMaskFieldNode(
            new ClassConstMaskNode(Name::createFromString('Foo')),
            new NamedTypeNode(Name::createFromString('int')),
        );

        $this->assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new ClassConstMaskFieldNode(
            new ClassConstMaskNode(Name::createFromString('Foo')),
            new NamedTypeNode(Name::createFromString('int')),
            true,
        );

        $this->assertTrue($node->isOptional);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ClassConstMaskFieldNode(
            new ClassConstMaskNode(Name::createFromString('A')),
            new NamedTypeNode(Name::createFromString('int')),
        );

        $this->assertSame(0, $node->offset);
    }
}
