<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class BoolLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorWithTrueValue(): void
    {
        $node = new BoolLiteralNode(true);

        self::assertTrue($node->value);
        self::assertSame('true', $node->raw);
    }

    #[Test]
    public function constructorWithFalseValue(): void
    {
        $node = new BoolLiteralNode(false);

        self::assertFalse($node->value);
        self::assertSame('false', $node->raw);
    }

    #[Test]
    public function constructorWithCustomRaw(): void
    {
        $node = new BoolLiteralNode(true, 'TRUE');

        self::assertTrue($node->value);
        self::assertSame('TRUE', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new BoolLiteralNode(true);

        self::assertSame('true', (string) $node);
    }

    #[Test]
    public function parseLowercaseTrue(): void
    {
        $node = BoolLiteralNode::parse('true');

        self::assertTrue($node->value);
        self::assertSame('true', $node->raw);
    }

    #[Test]
    public function parseUppercaseTrue(): void
    {
        $node = BoolLiteralNode::parse('TRUE');

        self::assertTrue($node->value);
    }

    #[Test]
    public function parseLowercaseFalse(): void
    {
        $node = BoolLiteralNode::parse('false');

        self::assertFalse($node->value);
    }

    #[Test]
    public function parseUppercaseFalse(): void
    {
        $node = BoolLiteralNode::parse('FALSE');

        self::assertFalse($node->value);
    }

    #[Test]
    public function parseNonTrueStringReturnsFalse(): void
    {
        $node = BoolLiteralNode::parse('yes');

        self::assertFalse($node->value);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new BoolLiteralNode(true);

        self::assertSame(0, $node->offset);
    }
}
