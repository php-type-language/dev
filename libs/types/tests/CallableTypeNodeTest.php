<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;

final class CallableTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $name = Name::createFromString('callable');
        $node = new CallableTypeNode($name);

        self::assertSame($name, $node->name);
    }

    #[Test]
    public function parametersDefaultToEmptyList(): void
    {
        $node = new CallableTypeNode(Name::createFromString('callable'));

        self::assertInstanceOf(CallableParameterListNode::class, $node->parameters);
        self::assertCount(0, $node->parameters);
    }

    #[Test]
    public function returnTypeDefaultsToNull(): void
    {
        $node = new CallableTypeNode(Name::createFromString('callable'));

        self::assertNull($node->type);
    }

    #[Test]
    public function constructorStoresParametersList(): void
    {
        $params = new CallableParameterListNode();
        $node = new CallableTypeNode(Name::createFromString('callable'), $params);

        self::assertSame($params, $node->parameters);
    }

    #[Test]
    public function constructorStoresReturnType(): void
    {
        $returnType = new NamedTypeNode(Name::createFromString('string'));
        $node = new CallableTypeNode(
            Name::createFromString('callable'),
            new CallableParameterListNode(),
            $returnType,
        );

        self::assertSame($returnType, $node->type);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new CallableTypeNode(Name::createFromString('callable'));

        self::assertSame(0, $node->offset);
    }
}
