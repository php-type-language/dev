<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ScalarFieldNodeTest extends TestCase
{
    #[Test]
    public function fromBuildsAnIntKey(): void
    {
        $field = ScalarFieldNode::from(0, NamedTypeNode::from('int'));

        self::assertInstanceOf(IntLiteralNode::class, $field->key);
        self::assertSame(0, $field->key->value);
    }

    #[Test]
    public function fromBuildsAStringKey(): void
    {
        $field = ScalarFieldNode::from('some key', NamedTypeNode::from('int'), false, 4);

        self::assertInstanceOf(StringLiteralNode::class, $field->key);
        self::assertSame('some key', $field->key->value);
        self::assertSame(4, $field->key->offset);
        self::assertSame(4, $field->offset);
    }
}
