<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Tests\TestCase;

final class NamedFieldNodeTest extends TestCase
{
    #[Test]
    public function constructorKeepsTheKeyTheTypeAndTheOffset(): void
    {
        $key = new Name('name', 3);
        $type = new NamedTypeNode(new Name('int'));

        $field = new NamedFieldNode($key, $type, true, 3);

        self::assertSame($key, $field->key);
        self::assertSame('name', $field->key->toString());
        self::assertSame($type, $field->type);
        self::assertTrue($field->isOptional);
        self::assertSame(3, $field->offset);
    }

    #[Test]
    public function fieldIsRequiredByDefault(): void
    {
        $field = new NamedFieldNode(new Name('name'), new NamedTypeNode(new Name('int')));

        self::assertFalse($field->isOptional);
        self::assertSame(0, $field->offset);
    }
}
