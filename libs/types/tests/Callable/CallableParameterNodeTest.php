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

        $this->assertSame($type, $node->type);
        $this->assertNull($node->name);
        $this->assertFalse($node->isOutput);
        $this->assertFalse($node->isVariadic);
        $this->assertFalse($node->isOptional);
        $this->assertNull($node->attributes);
    }

    #[Test]
    public function constructorWithNameOnly(): void
    {
        $name = VariableLiteralNode::parse('param');
        $node = new CallableParameterNode(name: $name);

        $this->assertNull($node->type);
        $this->assertSame($name, $node->name);
    }

    #[Test]
    public function constructorWithTypeAndName(): void
    {
        $type = new NamedTypeNode(Name::createFromString('int'));
        $name = VariableLiteralNode::parse('count');
        $node = new CallableParameterNode($type, $name);

        $this->assertSame($type, $node->type);
        $this->assertSame($name, $node->name);
    }

    #[Test]
    public function outputFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isOutput: true,
        );

        $this->assertTrue($node->isOutput);
    }

    #[Test]
    public function variadicFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isVariadic: true,
        );

        $this->assertTrue($node->isVariadic);
    }

    #[Test]
    public function optionalFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            isOptional: true,
        );

        $this->assertTrue($node->isOptional);
    }

    #[Test]
    public function toStringReturnsSimpleWhenNoFlags(): void
    {
        $node = new CallableParameterNode(type: new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame('simple', (string) $node);
    }

    #[Test]
    public function toStringReturnsOutputWhenOutputIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOutput: true,
        );

        $this->assertSame('output', (string) $node);
    }

    #[Test]
    public function toStringReturnsVariadicWhenVariadicIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isVariadic: true,
        );

        $this->assertSame('variadic', (string) $node);
    }

    #[Test]
    public function toStringReturnsOptionalWhenOptionalIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOptional: true,
        );

        $this->assertSame('optional', (string) $node);
    }

    #[Test]
    public function toStringCombinesMultipleFlags(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            isOutput: true,
            isOptional: true,
        );

        $this->assertSame('output, optional', (string) $node);
    }

    #[Test]
    public function throwsWhenBothTypeAndNameAreNull(): void
    {
        $this->expectException(\TypeError::class);
        new CallableParameterNode();
    }

    #[Test]
    public function throwsWhenBothVariadicAndOptionalAreTrue(): void
    {
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

        $this->assertSame(0, $node->offset);
    }
}
