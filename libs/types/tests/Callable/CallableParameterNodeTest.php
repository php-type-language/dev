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
        $this->assertFalse($node->output);
        $this->assertFalse($node->variadic);
        $this->assertFalse($node->optional);
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
            output: true,
        );

        $this->assertTrue($node->output);
    }

    #[Test]
    public function variadicFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            variadic: true,
        );

        $this->assertTrue($node->variadic);
    }

    #[Test]
    public function optionalFlagIsStored(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
            optional: true,
        );

        $this->assertTrue($node->optional);
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
            output: true,
        );

        $this->assertSame('output', (string) $node);
    }

    #[Test]
    public function toStringReturnsVariadicWhenVariadicIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            variadic: true,
        );

        $this->assertSame('variadic', (string) $node);
    }

    #[Test]
    public function toStringReturnsOptionalWhenOptionalIsSet(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            optional: true,
        );

        $this->assertSame('optional', (string) $node);
    }

    #[Test]
    public function toStringCombinesMultipleFlags(): void
    {
        $node = new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
            output: true,
            optional: true,
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
            variadic: true,
            optional: true,
        );
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new CallableParameterNode(type: new NamedTypeNode(Name::createFromString('int')));

        $this->assertSame(0, $node->offset);
    }
}
