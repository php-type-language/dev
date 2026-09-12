<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Callable;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\VariableNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\Tests\TestCase;

final class CallableParameterNodeTest extends TestCase
{
    #[Test]
    public function constructorWithTypeOnly(): void
    {
        $type = new NamedTypeNode(Name::createFromString('string'));
        $node = new CallableParameterNode(type: $type);

        self::assertSame($type, $node->type);
        self::assertNull($node->name);
        self::assertFalse($node->isOutput);
        self::assertFalse($node->isVariadic);
        self::assertFalse($node->isOptional);
    }

    #[Test]
    public function constructorWithNameOnly(): void
    {
        $name = new VariableNode(new Identifier('param'));
        $node = new CallableParameterNode(name: $name);

        self::assertNull($node->type);
        self::assertSame($name, $node->name);
    }

    #[Test]
    public function constructorWithTypeAndName(): void
    {
        $type = new NamedTypeNode(Name::createFromString('int'));
        $name = new VariableNode(new Identifier('count'));
        $node = new CallableParameterNode($type, $name);

        self::assertSame($type, $node->type);
        self::assertSame($name, $node->name);
    }

    #[Test]
    public function outputFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isOutput: true,
        );

        self::assertTrue($node->isOutput);
    }

    #[Test]
    public function variadicFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isVariadic: true,
        );

        self::assertTrue($node->isVariadic);
    }

    #[Test]
    public function optionalFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isOptional: true,
        );

        self::assertTrue($node->isOptional);
    }

    #[Test]
    public function throwsWhenBothTypeAndNameAreNull(): void
    {
        self::skipWhenAssertsAreDisabled();

        $this->expectException(\TypeError::class);

        new CallableParameterNode();
    }

    #[Test]
    public function throwsWhenBothVariadicAndOptionalAreTrue(): void
    {
        self::skipWhenAssertsAreDisabled();

        $this->expectException(\TypeError::class);

        new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isVariadic: true,
            isOptional: true,
        );
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new CallableParameterNode(type: new NamedTypeNode(Name::createFromString('int')));

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function isReturnsTrueForOwnClass(): void
    {
        $parameter = new CallableParameterNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertTrue($parameter->is(CallableParameterNode::class));
        self::assertTrue($parameter->is(Node::class));
    }

    #[Test]
    public function isReturnsFalseForAnotherClass(): void
    {
        $parameter = new CallableParameterNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertFalse($parameter->is(NamedTypeNode::class));
    }
}
