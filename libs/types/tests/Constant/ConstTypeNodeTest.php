<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Tests\TestCase;

final class ConstTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorKeepsTheClassTheConstantAndTheOffset(): void
    {
        $class = new Name('Any', new NamespaceName('Some', NamespaceKind::FullyQualified));
        $constant = new Identifier('CONST_NAME');

        $node = new ConstTypeNode($class, $constant, 5);

        self::assertSame($class, $node->owner);
        self::assertSame($constant, $node->constant);
        self::assertSame('\Some\Any', $node->owner->toString());
        self::assertSame('CONST_NAME', $node->constant->toString());
        self::assertSame(5, $node->offset);
    }

    #[Test]
    public function constantMayBelongToANamespace(): void
    {
        $namespace = new NamespaceName('Some\Any');

        $node = new ConstTypeNode($namespace, new Identifier('CONST_NAME'));

        self::assertSame($namespace, $node->owner);
    }
}
