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

        $this->assertCount(0, $group);
        $this->assertNull($group->first);
        $this->assertNull($group->last);
    }

    #[Test]
    public function constructorAcceptsAttributes(): void
    {
        $a = $this->makeAttr('Pure');
        $b = $this->makeAttr('Deprecated');
        $group = new AttributeGroupNode([$a, $b]);

        $this->assertCount(2, $group);
        $this->assertSame($a, $group->first);
        $this->assertSame($b, $group->last);
    }

    #[Test]
    public function findIndexReturnsPosition(): void
    {
        $a = $this->makeAttr('Pure');
        $b = $this->makeAttr('Deprecated');
        $group = new AttributeGroupNode([$a, $b]);

        $this->assertSame(0, $group->findIndex($a));
        $this->assertSame(1, $group->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullWhenNotFound(): void
    {
        $group = new AttributeGroupNode([$this->makeAttr('Pure')]);

        $this->assertNull($group->findIndex($this->makeAttr('Other')));
    }

    #[Test]
    public function iteratorYieldsAttributes(): void
    {
        $a = $this->makeAttr('A');
        $b = $this->makeAttr('B');
        $group = new AttributeGroupNode([$a, $b]);

        $this->assertSame([$a, $b], \iterator_to_array($group));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $group = new AttributeGroupNode();

        $this->assertSame(0, $group->offset);
    }
}
