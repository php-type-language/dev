<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class StringLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueAndRaw(): void
    {
        $node = new StringLiteralNode('hello', '"hello"');

        self::assertSame('hello', $node->value);
        self::assertSame('"hello"', $node->raw);
    }

    #[Test]
    public function constructorDerivesRawWhenOmitted(): void
    {
        $node = new StringLiteralNode('hello');

        self::assertSame('hello', $node->value);
        self::assertSame('"hello"', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new StringLiteralNode('hello', '"hello"');

        self::assertSame('"hello"', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new StringLiteralNode('test');

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function constructorEscapesTheDerivedRawValue(): void
    {
        $node = new StringLiteralNode('a"b');

        self::assertSame('"a\"b"', $node->raw);
        self::assertSame('a"b', $node->value);
    }
}
