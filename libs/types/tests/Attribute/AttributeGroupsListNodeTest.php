<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Attribute;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Attribute\AttributeGroupNode;
use TypeLang\Type\Attribute\AttributeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Tests\TestCase;

final class AttributeGroupsListNodeTest extends TestCase
{
    private function makeGroup(string ...$names): AttributeGroupNode
    {
        $attrs = \array_map(
            static fn(string $n) => new AttributeNode(Name::createFromString($n)),
            $names,
        );

        return new AttributeGroupNode($attrs);
    }

    #[Test]
    public function emptyListByDefault(): void
    {
        $list = new AttributeGroupListNode();

        self::assertCount(0, $list);
        self::assertNull($list->first);
        self::assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsGroups(): void
    {
        $g1 = $this->makeGroup('Pure');
        $g2 = $this->makeGroup('Deprecated');
        $list = new AttributeGroupListNode([$g1, $g2]);

        self::assertCount(2, $list);
        self::assertSame($g1, $list->first);
        self::assertSame($g2, $list->last);
    }

    #[Test]
    public function arrayAccessWorks(): void
    {
        $g = $this->makeGroup('Pure');
        $list = new AttributeGroupListNode([$g]);

        self::assertTrue(isset($list[0]));
        self::assertSame($g, $list[0]);
        self::assertNull($list[1]);
    }

    #[Test]
    public function unsetRemovesAndReindexes(): void
    {
        $g1 = $this->makeGroup('A');
        $g2 = $this->makeGroup('B');
        $list = new AttributeGroupListNode([$g1, $g2]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($g2, $list[0]);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new AttributeGroupListNode();

        self::assertSame(0, $list->offset);
    }
}
