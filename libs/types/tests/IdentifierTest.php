<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Node;

final class IdentifierTest extends TestCase
{
    #[Test]
    public function valueAndOffsetAreKept(): void
    {
        $identifier = new Identifier('CONST_NAME', 7);

        self::assertSame('CONST_NAME', $identifier->value);
        self::assertSame(7, $identifier->offset);
    }

    #[Test]
    public function offsetIsZeroByDefault(): void
    {
        self::assertSame(0, (new Identifier('Foo'))->offset);
    }

    #[Test]
    public function stringFormIsTheValue(): void
    {
        $identifier = new Identifier('non-empty-string');

        self::assertSame('non-empty-string', $identifier->toString());
        self::assertSame('non-empty-string', (string) $identifier);
    }

    #[Test]
    public function emptyValueIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // @phpstan-ignore-next-line : An empty string is what the test passes on purpose
        new Identifier('');
    }

    #[Test]
    public function identifierIsANodeAndStringable(): void
    {
        $identifier = new Identifier('Foo');

        self::assertInstanceOf(Node::class, $identifier);
        self::assertInstanceOf(\Stringable::class, $identifier);
    }
}
