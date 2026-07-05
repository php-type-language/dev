<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Callable;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
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
        self::assertNull($node->attributes);
    }

    #[Test]
    public function constructorWithNameOnly(): void
    {
        $name = VariableLiteralNode::parse('param');
        $node = new CallableParameterNode(name: $name);

        self::assertNull($node->type);
        self::assertSame($name, $node->name);
    }

    #[Test]
    public function constructorWithTypeAndName(): void
    {
        $type = new NamedTypeNode(Name::createFromString('int'));
        $name = VariableLiteralNode::parse('count');
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
    public function toStringReturnsSimpleWhenNoFlags(): void
    {
        $node = new CallableParameterNode(type: new NamedTypeNode(Name::createFromString('int')));

        self::assertSame('simple', (string) $node);
    }

    #[Test]
    public function toStringReturnsOutputWhenOutputIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOutput: true,
        );

        self::assertSame('output', (string) $node);
    }

    #[Test]
    public function toStringReturnsVariadicWhenVariadicIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isVariadic: true,
        );

        self::assertSame('variadic', (string) $node);
    }

    #[Test]
    public function toStringReturnsOptionalWhenOptionalIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOptional: true,
        );

        self::assertSame('optional', (string) $node);
    }

    #[Test]
    public function toStringCombinesMultipleFlags(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOutput: true,
            isOptional: true,
        );

        self::assertSame('output, optional', (string) $node);
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
}
