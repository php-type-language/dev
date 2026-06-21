<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class IntLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueRawAndDecimal(): void
    {
        $node = new IntLiteralNode(42, '42', '42');

        $this->assertSame(42, $node->value);
        $this->assertSame('42', $node->raw);
        $this->assertSame('42', $node->decimal);
    }

    #[Test]
    public function constructorWithCustomRawAndDecimal(): void
    {
        $node = new IntLiteralNode(42, '0x2A', '42');

        $this->assertSame(42, $node->value);
        $this->assertSame('0x2A', $node->raw);
        $this->assertSame('42', $node->decimal);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new IntLiteralNode(42, '0x2A', '42');

        $this->assertSame('0x2A', (string) $node);
    }

    #[Test]
    #[DataProvider('provideDecimalIntegers')]
    public function parseDecimalInteger(string $input, int $expected): void
    {
        $node = IntLiteralNode::parse($input);

        $this->assertSame($expected, $node->value);
        $this->assertSame($input, $node->raw);
    }

    public static function provideDecimalIntegers(): iterable
    {
        return [
            ['0', 0],
            ['1', 1],
            ['42', 42],
            ['100', 100],
        ];
    }

    #[Test]
    public function parseNegativeInteger(): void
    {
        $node = IntLiteralNode::parse('-42');

        $this->assertSame(-42, $node->value);
    }

    #[Test]
    public function parseHexadecimalInteger(): void
    {
        $node = IntLiteralNode::parse('0xFF');

        $this->assertSame(255, $node->value);
    }

    #[Test]
    public function parseBinaryInteger(): void
    {
        $node = IntLiteralNode::parse('0b1010');

        $this->assertSame(10, $node->value);
    }

    #[Test]
    public function parseOctalInteger(): void
    {
        $node = IntLiteralNode::parse('0o17');

        $this->assertSame(15, $node->value);
    }

    #[Test]
    public function parseLegacyOctalInteger(): void
    {
        $node = IntLiteralNode::parse('017');

        $this->assertSame(15, $node->value);
    }

    #[Test]
    public function parseIntegerWithUnderscores(): void
    {
        $node = IntLiteralNode::parse('1_000_000');

        $this->assertSame(1000000, $node->value);
    }

    #[Test]
    public function parsePhpIntMin(): void
    {
        $node = IntLiteralNode::parse((string) \PHP_INT_MIN);

        $this->assertSame(\PHP_INT_MIN, $node->value);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = IntLiteralNode::parse('0');

        $this->assertSame(0, $node->offset);
    }
}
