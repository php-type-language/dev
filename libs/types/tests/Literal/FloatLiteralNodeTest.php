<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class FloatLiteralNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValueAndRaw(): void
    {
        $node = new FloatLiteralNode(3.14, '3.14');

        self::assertSame(3.14, $node->value);
        self::assertSame('3.14', $node->raw);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new FloatLiteralNode(3.14, '3.14');

        self::assertSame('3.14', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new FloatLiteralNode(0.0);

        self::assertSame(0, $node->offset);
    }
}
