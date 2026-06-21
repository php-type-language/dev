<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Name;

final class ConstMaskNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $name = Name::createFromString('SOME_CONST');
        $node = new ConstMaskNode($name);

        $this->assertSame($name, $node->name);
    }

    #[Test]
    public function toStringAppendsAsterisk(): void
    {
        $node = new ConstMaskNode(Name::createFromString('SOME_CONST'));

        $this->assertSame('SOME_CONST*', (string) $node);
    }

    #[Test]
    public function toStringWithQualifiedNameAppendsAsterisk(): void
    {
        $node = new ConstMaskNode(Name::createFromString('Vendor\Package\STATUS'));

        $this->assertSame('Vendor\Package\STATUS*', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ConstMaskNode(Name::createFromString('FOO'));

        $this->assertSame(0, $node->offset);
    }
}
