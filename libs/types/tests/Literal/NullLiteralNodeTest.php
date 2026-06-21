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

        $this->assertNull($node->value);
    }

    #[Test]
    public function rawDefaultsToNullString(): void
    {
        $node = new NullLiteralNode();

        $this->assertSame('null', $node->raw);
    }

    #[Test]
    public function constructorAcceptsCustomRaw(): void
    {
        $node = new NullLiteralNode('NULL');

        $this->assertSame('NULL', $node->raw);
        $this->assertNull($node->value);
    }

    #[Test]
    public function toStringReturnsRaw(): void
    {
        $node = new NullLiteralNode();

        $this->assertSame('null', (string) $node);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NullLiteralNode();

        $this->assertSame(0, $node->offset);
    }
}
