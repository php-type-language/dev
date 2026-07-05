<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Attribute;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeGroupNode;
use TypeLang\Type\Attribute\AttributeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Tests\TestCase;

final class AttributeGroupNodeTest extends TestCase
{
    private function makeAttr(string $name): AttributeNode
    {
        return new AttributeNode(Name::createFromString($name));
    }

    #[Test]
    public function emptyGroupByDefault(): void
    {
        $group = new AttributeGroupNode();

        self::assertCount(0, $group);
        self::assertNull($group->first);
        self::assertNull($group->last);
    }

    #[Test]
    public function constructorAcceptsAttributes(): void
    {
        $a = $this->makeAttr('Pure');
        $b = $this->makeAttr('Deprecated');
        $group = new AttributeGroupNode([$a, $b]);

        self::assertCount(2, $group);
        self::assertSame($a, $group->first);
        self::assertSame($b, $group->last);
    }

    #[Test]
    public function findIndexReturnsPosition(): void
    {
        $a = $this->makeAttr('Pure');
        $b = $this->makeAttr('Deprecated');
        $group = new AttributeGroupNode([$a, $b]);

        self::assertSame(0, $group->findIndex($a));
        self::assertSame(1, $group->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullWhenNotFound(): void
    {
        $group = new AttributeGroupNode([$this->makeAttr('Pure')]);

        self::assertNull($group->findIndex($this->makeAttr('Other')));
    }

    #[Test]
    public function iteratorYieldsAttributes(): void
    {
        $a = $this->makeAttr('A');
        $b = $this->makeAttr('B');
        $group = new AttributeGroupNode([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($group));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $group = new AttributeGroupNode();

        self::assertSame(0, $group->offset);
    }
}
