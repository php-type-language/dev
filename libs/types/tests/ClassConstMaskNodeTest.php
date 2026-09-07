<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\MaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\WildcardNode;

final class ClassConstMaskNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresClassWithMask(): void
    {
        $class = Name::createFromString('MyClass');
        $mask = new MaskNode([new Identifier('STATUS_'), new WildcardNode()]);
        $node = new ClassConstMaskNode($class, $mask);

        self::assertSame($class, $node->class);
        self::assertSame($mask, $node->mask);
    }

    #[Test]
    public function aMaskOfNothingButAWildcardCarriesNoSegments(): void
    {
        $node = new ClassConstMaskNode(
            Name::createFromString('MyEnum'),
            new MaskNode([new WildcardNode()]),
        );

        self::assertSame([], $node->mask->getSegments());
        self::assertSame('*', $node->mask->toString());
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ClassConstMaskNode(
            Name::createFromString('Foo'),
            new MaskNode([new WildcardNode()]),
        );

        self::assertSame(0, $node->offset);
    }
}
