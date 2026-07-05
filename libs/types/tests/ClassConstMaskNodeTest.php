<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

final class ClassConstMaskNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresClassWithConstant(): void
    {
        $class = Name::createFromString('MyClass');
        $const = new Identifier('STATUS_');
        $node = new ClassConstMaskNode($class, $const);

        self::assertSame($class, $node->class);
        self::assertSame($const, $node->constant);
    }

    #[Test]
    public function constantDefaultsToNull(): void
    {
        $class = Name::createFromString('MyClass');
        $node = new ClassConstMaskNode($class);

        self::assertNull($node->constant);
    }

    #[Test]
    public function constructorAcceptsNullConstant(): void
    {
        $class = Name::createFromString('MyEnum');
        $node = new ClassConstMaskNode($class, null);

        self::assertSame($class, $node->class);
        self::assertNull($node->constant);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ClassConstMaskNode(Name::createFromString('Foo'));

        self::assertSame(0, $node->offset);
    }
}
