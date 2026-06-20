<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Node\Literal\StringLiteralNode;

/**
 * Tests for the string literal grammar (single and double quoted strings).
 *
 * @see \TypeLang\Parser\Node\Literal\StringLiteralNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class StringLiteralTest extends SyntaxTestCase
{
    public function testDoubleQuotedString(): void
    {
        $statement = $this->parse('"hello world"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('hello world', $statement->getValue());
    }

    public function testSingleQuotedString(): void
    {
        $statement = $this->parse("'hello world'");

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('hello world', $statement->getValue());
    }

    public function testEmptyDoubleQuotedString(): void
    {
        $statement = $this->parse('""');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('', $statement->getValue());
    }

    public function testEmptySingleQuotedString(): void
    {
        $statement = $this->parse("''");

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('', $statement->getValue());
    }

    public function testEscapedSingleQuoteInsideSingleQuotedString(): void
    {
        $statement = $this->parse("'I am single-quoted \\' string'");

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame("I am single-quoted ' string", $statement->getValue());
    }

    public function testEscapedDoubleQuoteInsideDoubleQuotedString(): void
    {
        $statement = $this->parse('"I am double-quoted \\" string"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('I am double-quoted " string', $statement->getValue());
    }

    public function testDoubleQuotedStringProcessesEscapeSequences(): void
    {
        $statement = $this->parse('"String with\\nNew Line"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame("String with\nNew Line", $statement->getValue());
    }

    public function testSingleQuotedStringIgnoresEscapeSequences(): void
    {
        $statement = $this->parse("'String without\\nNew Line'");

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('String without\\nNew Line', $statement->getValue());
    }

    public function testHexadecimalEscapeSequence(): void
    {
        $statement = $this->parse('"\\x48\\x65\\x6c\\x6c\\x6f"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('Hello', $statement->getValue());
    }

    public function testHexadecimalSequenceIsIgnoredInSingleQuotedString(): void
    {
        $statement = $this->parse("'\\x48\\x65'");

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('\\x48\\x65', $statement->getValue());
    }

    public function testUnicodeEscapeSequence(): void
    {
        $statement = $this->parse('"\\u{41}"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('A', $statement->getValue());
    }

    public function testUnicodeEscapeSequenceWithSurrogatePair(): void
    {
        $statement = $this->parse('"\\u{1F60A}"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame("\u{1F60A}", $statement->getValue());
    }

    public function testDollarEscapeSequence(): void
    {
        $statement = $this->parse('"price \\$value"');

        self::assertInstanceOf(StringLiteralNode::class, $statement);
        self::assertSame('price $value', $statement->getValue());
    }

    public function testUnterminatedDoubleQuotedString(): void
    {
        $this->expectParsingException();

        $this->parse('"unterminated');
    }

    public function testUnterminatedSingleQuotedString(): void
    {
        $this->expectParsingException();

        $this->parse("'unterminated");
    }
}
