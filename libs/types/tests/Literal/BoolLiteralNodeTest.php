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

        $this->assertTrue($node->value);
        $this->assertSame('true', $node->raw);
    }

    #[Test]
    public function constructorWithFalseValue(): void
    {
        $node = new BoolLiteralNode(false);

        $this->assertFalse($node->value);
        $this->assertSame('false', $node->raw);
    }

    #[Test]
    public function constructorWithCustomRaw(): void
    {
        $node = new BoolLiteralNode(true, 'TRUE');

        $this->assertTrue($node->value);
        $this->assertSame('TRUE', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new BoolLiteralNode(true);

        $this->assertSame('true', (string) $node);
    }

    #[Test]
    public function parseLowercaseTrue(): void
    {
        $node = BoolLiteralNode::parse('true');

        $this->assertTrue($node->value);
        $this->assertSame('true', $node->raw);
    }

    #[Test]
    public function parseUppercaseTrue(): void
    {
        $node = BoolLiteralNode::parse('TRUE');

        $this->assertTrue($node->value);
    }

    #[Test]
    public function parseLowercaseFalse(): void
    {
        $node = BoolLiteralNode::parse('false');

        $this->assertFalse($node->value);
    }

    #[Test]
    public function parseUppercaseFalse(): void
    {
        $node = BoolLiteralNode::parse('FALSE');

        $this->assertFalse($node->value);
    }

    #[Test]
    public function parseNonTrueStringReturnsFalse(): void
    {
        $node = BoolLiteralNode::parse('yes');

        $this->assertFalse($node->value);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new BoolLiteralNode(true);

        $this->assertSame(0, $node->offset);
    }
}
