<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Attribute;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeArgumentListNode;
use TypeLang\Type\Attribute\AttributeArgumentNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class AttributeArgumentsListNodeTest extends TestCase
{
    private function makeArg(string $type): AttributeArgumentNode
    {
        return new AttributeArgumentNode(new NamedTypeNode(Name::createFromString($type)));
    }

    #[Test]
    public function emptyListByDefault(): void
    {
        $list = new AttributeArgumentListNode();

        self::assertCount(0, $list);
        self::assertNull($list->first);
        self::assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsItems(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a, $b]);

        self::assertCount(2, $list);
    }

    #[Test]
    public function firstAndLastProperties(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a, $b]);

        self::assertSame($a, $list->first);
        self::assertSame($b, $list->last);
    }

    #[Test]
    public function arrayAccessOffsetExists(): void
    {
        $list = new AttributeArgumentListNode([$this->makeArg('int')]);

        self::assertTrue(isset($list[0]));
        self::assertFalse(isset($list[1]));
    }

    #[Test]
    public function arrayAccessOffsetGet(): void
    {
        $arg = $this->makeArg('string');
        $list = new AttributeArgumentListNode([$arg]);

        self::assertSame($arg, $list[0]);
        self::assertNull($list[1]);
    }

    #[Test]
    public function arrayAccessOffsetSet(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a]);
        $list[0] = $b;

        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a, $b]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a, $b]);

        self::assertSame(0, $list->findIndex($a));
        self::assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullForAbsentNode(): void
    {
        $list = new AttributeArgumentListNode([$this->makeArg('int')]);
        $absent = $this->makeArg('string');

        self::assertNull($list->findIndex($absent));
    }

    #[Test]
    public function iteratorYieldsItems(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new AttributeArgumentListNode([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new AttributeArgumentListNode();

        self::assertSame(0, $list->offset);
    }
}
