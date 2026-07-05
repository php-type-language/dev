<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class NullLiteralNodeTest extends TestCase
{
    #[Test]
    public function valueIsNull(): void
    {
        $node = new NullLiteralNode();

        self::assertNull($node->value);
    }

    #[Test]
    public function rawDefaultsToNullString(): void
    {
        $node = new NullLiteralNode();

        self::assertSame('null', $node->raw);
    }

    #[Test]
    public function constructorAcceptsCustomRaw(): void
    {
        $node = new NullLiteralNode('NULL');

        self::assertSame('NULL', $node->raw);
        self::assertNull($node->value);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new NullLiteralNode();

        self::assertSame('null', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NullLiteralNode();

        self::assertSame(0, $node->offset);
    }
}
