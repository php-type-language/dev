<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ClassConstFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ClassConstFieldNodeTest extends TestCase
{
    private function makeKey(string $class, string $const): ClassConstNode
    {
        return new ClassConstNode(Name::createFromString($class), new Identifier($const));
    }

    #[Test]
    public function constructorStoresKeyAndType(): void
    {
        $key = $this->makeKey('MyClass', 'STATUS');
        $type = new NamedTypeNode(Name::createFromString('int'));
        $node = new ClassConstFieldNode($key, $type);

        self::assertSame($key, $node->key);
        self::assertSame($type, $node->type);
    }

    #[Test]
    public function indexFormatsAsClassDoubleColonConst(): void
    {
        $key = $this->makeKey('MyEnum', 'ACTIVE');
        $node = new ClassConstFieldNode($key, new NamedTypeNode(Name::createFromString('string')));

        self::assertSame('MyEnum::ACTIVE', $node->index);
    }

    #[Test]
    public function indexWithQualifiedClassName(): void
    {
        $key = $this->makeKey('Vendor\Package\Status', 'OK');
        $node = new ClassConstFieldNode($key, new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('Vendor\Package\Status::OK', $node->index);
    }

    #[Test]
    public function optionalDefaultsToFalse(): void
    {
        $node = new ClassConstFieldNode(
            $this->makeKey('Foo', 'BAR'),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorStoresOptionalFlag(): void
    {
        $node = new ClassConstFieldNode(
            $this->makeKey('Foo', 'BAR'),
            new NamedTypeNode(Name::createFromString('int')),
            true,
        );

        self::assertTrue($node->isOptional);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ClassConstFieldNode(
            $this->makeKey('A', 'B'),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertSame(0, $node->offset);
    }
}
