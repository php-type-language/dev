<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\LiteralNodeInterface;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

final class LiteralNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{LiteralNode, mixed, non-empty-string}>
     */
    public static function provideLiterals(): iterable
    {
        yield 'bool(true)' => [new BoolLiteralNode(true, 'true'), true, 'true'];
        yield 'bool(false)' => [new BoolLiteralNode(false, 'false'), false, 'false'];
        yield 'float' => [new FloatLiteralNode(0.5, '0.5'), 0.5, '0.5'];
        yield 'int' => [new IntLiteralNode(42, '42', '42'), 42, '42'];
        yield 'null' => [new NullLiteralNode(), null, 'null'];
        yield 'string' => [new StringLiteralNode('example', '"example"'), 'example', '"example"'];
    }

    #[Test]
    #[DataProvider('provideLiterals')]
    public function valueMethodIsAnAliasOfValueProperty(LiteralNode $node, mixed $value, string $raw): void
    {
        self::assertSame($value, $node->value);
        self::assertSame($value, $node->value);
    }

    #[Test]
    #[DataProvider('provideLiterals')]
    public function rawMethodIsAnAliasOfRawProperty(LiteralNode $node, mixed $value, string $raw): void
    {
        self::assertSame($raw, $node->raw);
        self::assertSame($raw, $node->raw);
    }

    #[Test]
    #[DataProvider('provideLiterals')]
    public function stringRepresentationIsTheRawValue(LiteralNode $node, mixed $value, string $raw): void
    {
        self::assertSame($raw, (string) $node);
    }

    #[Test]
    #[DataProvider('provideLiterals')]
    public function everyLiteralImplementsItsContracts(LiteralNode $node, mixed $value, string $raw): void
    {
        self::assertInstanceOf(LiteralNodeInterface::class, $node);
        self::assertInstanceOf(TypeNode::class, $node);
        self::assertInstanceOf(\Stringable::class, $node);
    }

    #[Test]
    public function boolLiteralDerivesRawFromValue(): void
    {
        self::assertSame('true', (new BoolLiteralNode(true))->raw);
        self::assertSame('false', (new BoolLiteralNode(false))->raw);
    }

    #[Test]
    public function nullLiteralDerivesRawFromValue(): void
    {
        self::assertSame('null', (new NullLiteralNode())->raw);
        self::assertNull((new NullLiteralNode())->value);
    }

    #[Test]
    public function stringLiteralDerivesRawFromValue(): void
    {
        self::assertSame("'example'", (new StringLiteralNode('example'))->raw);
    }

    #[Test]
    public function intLiteralDerivesRawFromValue(): void
    {
        $node = new IntLiteralNode(42);

        self::assertSame(42, $node->value);
        self::assertSame('42', $node->raw);
        self::assertSame('42', $node->decimal);
    }

    #[Test]
    public function negativeIntLiteralDerivesRawFromValue(): void
    {
        $node = new IntLiteralNode(-42);

        self::assertSame('-42', $node->raw);
        self::assertSame('-42', $node->decimal);
    }

    #[Test]
    public function floatLiteralDerivesRawFromValue(): void
    {
        $node = new FloatLiteralNode(0.5);

        self::assertSame(0.5, $node->value);
        self::assertSame('0.5', $node->raw);
    }

    #[Test]
    public function literalValueAndRawArePartOfTheNodeState(): void
    {
        $node = new StringLiteralNode('example');
        $node->offset = 5;

        self::assertSame(5, $node->offset);
        self::assertSame('example', $node->value);
    }
}
