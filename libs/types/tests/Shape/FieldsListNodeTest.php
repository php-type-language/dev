<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Tests\TestCase;

final class FieldsListNodeTest extends TestCase
{
    private function makeField(string $type = 'string'): ImplicitFieldNode
    {
        return new ImplicitFieldNode(new NamedTypeNode(Name::createFromString($type)));
    }

    #[Test]
    public function emptyListByDefault(): void
    {
        $list = new FieldsListNode();

        self::assertCount(0, $list);
        self::assertNull($list->first);
        self::assertNull($list->last);
    }

    #[Test]
    public function sealedByDefault(): void
    {
        $list = new FieldsListNode();

        self::assertTrue($list->sealed);
    }

    #[Test]
    public function constructorAcceptsUnsealedFlag(): void
    {
        $list = new FieldsListNode([], false);

        self::assertFalse($list->sealed);
    }

    #[Test]
    public function constructorAcceptsFields(): void
    {
        $a = $this->makeField('string');
        $b = $this->makeField('int');
        $list = new FieldsListNode([$a, $b]);

        self::assertCount(2, $list);
        self::assertSame($a, $list->first);
        self::assertSame($b, $list->last);
    }

    #[Test]
    public function toStringReturnsSealedWhenSealed(): void
    {
        $list = new FieldsListNode([], true);

        self::assertSame('sealed', (string) $list);
    }

    #[Test]
    public function toStringReturnsUnsealedWhenNotSealed(): void
    {
        $list = new FieldsListNode([], false);

        self::assertSame('unsealed', (string) $list);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeField('string');
        $b = $this->makeField('int');
        $list = new FieldsListNode([$a, $b]);

        self::assertSame(0, $list->findIndex($a));
        self::assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullForAbsentNode(): void
    {
        $list = new FieldsListNode([$this->makeField('int')]);

        self::assertNull($list->findIndex($this->makeField('string')));
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeField('string');
        $b = $this->makeField('int');
        $list = new FieldsListNode([$a, $b]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function iteratorYieldsFields(): void
    {
        $a = $this->makeField('string');
        $b = $this->makeField('int');
        $list = new FieldsListNode([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new FieldsListNode();

        self::assertSame(0, $list->offset);
    }
}
