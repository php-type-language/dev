<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Tests\TestCase;

final class NamedFieldNodeTest extends TestCase
{
    #[Test]
    public function fromBuildsTheKeyFromAString(): void
    {
        $type = NamedTypeNode::from('int');
        $field = NamedFieldNode::from('name', $type, true, 3);

        self::assertSame('name', $field->key->value);
        self::assertSame($type, $field->type);
        self::assertTrue($field->isOptional);
        self::assertSame(3, $field->offset);
    }
}
