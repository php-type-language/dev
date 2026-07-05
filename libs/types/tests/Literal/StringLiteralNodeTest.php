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
    public function parseDoubleQuotedString(): void
    {
        $node = StringLiteralNode::parse('"hello"');

        self::assertSame('hello', $node->value);
        self::assertSame('"hello"', $node->raw);
    }

    #[Test]
    public function parseSingleQuotedString(): void
    {
        $node = StringLiteralNode::parse("'hello'");

        self::assertSame('hello', $node->value);
        self::assertSame("'hello'", $node->raw);
    }

    #[Test]
    public function parseDoubleQuotedWithEscapedQuote(): void
    {
        $node = StringLiteralNode::parse('"say \\"hello\\""');

        self::assertSame('say "hello"', $node->value);
    }

    #[Test]
    public function parseSingleQuotedWithEscapedQuote(): void
    {
        $node = StringLiteralNode::parse("'it\\'s'");

        self::assertSame("it's", $node->value);
    }

    #[Test]
    public function parseNewlineEscapeSequence(): void
    {
        $node = StringLiteralNode::parse('"line1\\nline2"');

        self::assertSame("line1\nline2", $node->value);
    }

    #[Test]
    public function parseTabEscapeSequence(): void
    {
        $node = StringLiteralNode::parse('"col1\\tcol2"');

        self::assertSame("col1\tcol2", $node->value);
    }

    #[Test]
    public function parseHexSequence(): void
    {
        $node = StringLiteralNode::parse('"\\x41"');

        self::assertSame('A', $node->value);
    }

    #[Test]
    public function parseUnicodeSequence(): void
    {
        $node = StringLiteralNode::parse('"\\u{0041}"');

        self::assertSame('A', $node->value);
    }

    #[Test]
    public function createFromDoubleQuotedString(): void
    {
        $node = StringLiteralNode::createFromDoubleQuotedString('"world"');

        self::assertSame('world', $node->value);
    }

    #[Test]
    public function createFromSingleQuotedString(): void
    {
        $node = StringLiteralNode::createFromSingleQuotedString("'world'");

        self::assertSame('world', $node->value);
    }

    #[Test]
    public function parseThrowsOnStringTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StringLiteralNode::parse('"');
    }

    #[Test]
    public function parseThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StringLiteralNode::parse('');
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new StringLiteralNode('test');

        self::assertSame(0, $node->offset);
    }
}
