<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

final class ClassConstNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresClassAndConstant(): void
    {
        $class = Name::createFromString('MyClass');
        $const = new Identifier('MY_CONST');
        $node = new ClassConstNode($class, $const);

        self::assertSame($class, $node->class);
        self::assertSame($const, $node->constant);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ClassConstNode(
            Name::createFromString('Foo'),
            new Identifier('BAR'),
        );

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function supportsFullyQualifiedClassName(): void
    {
        $class = Name::createFromString('\Vendor\Package\MyClass');
        $const = new Identifier('STATUS');
        $node = new ClassConstNode($class, $const);

        self::assertTrue($node->class->isFullyQualified);
        self::assertSame('STATUS', $node->constant->value);
    }
}
