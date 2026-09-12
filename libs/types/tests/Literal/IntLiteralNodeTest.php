<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class IntLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueRawAndDecimal(): void
    {
        $node = new IntLiteralNode(42, '42', '42');

        self::assertSame(42, $node->value);
        self::assertSame('42', $node->raw);
        self::assertSame('42', $node->decimal);
    }

    #[Test]
    public function constructorWithCustomRawAndDecimal(): void
    {
        $node = new IntLiteralNode(42, '0x2A', '42');

        self::assertSame(42, $node->value);
        self::assertSame('0x2A', $node->raw);
        self::assertSame('42', $node->decimal);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new IntLiteralNode(42, '0x2A', '42');

        self::assertSame('0x2A', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new IntLiteralNode(0);

        self::assertSame(0, $node->offset);
    }
}
