<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ScalarFieldNodeTest extends TestCase
{
    #[Test]
    public function intKeyIsKept(): void
    {
        $key = new IntLiteralNode(0);
        $type = new NamedTypeNode(new Name('int'));

        $field = new ScalarFieldNode($key, $type);

        self::assertSame($key, $field->key);
        self::assertSame($type, $field->type);
        self::assertSame(0, $field->key->value);
        self::assertFalse($field->isOptional);
    }

    #[Test]
    public function stringKeyIsKeptWithItsOffsetAndTheOptionalMark(): void
    {
        $key = new StringLiteralNode('some key', offset: 4);

        $field = new ScalarFieldNode($key, new NamedTypeNode(new Name('int')), true, 4);

        self::assertSame($key, $field->key);
        self::assertSame('some key', $field->key->value);
        self::assertSame(4, $field->key->offset);
        self::assertSame(4, $field->offset);
        self::assertTrue($field->isOptional);
    }
}
