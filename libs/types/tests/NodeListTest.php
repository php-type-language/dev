<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\NodeList;

final class NodeListTest extends TestCase
{
    /**
     * @param list<Node> $items
     * @return NodeList<Node>
     */
    private function list(array $items = []): NodeList
    {
        /** @var NodeList<Node> */
        return new class ($items) extends NodeList {};
    }

    private function node(string $name = 'Example'): Identifier
    {
        return new Identifier($name);
    }

    #[Test]
    public function emptyByDefault(): void
    {
        $list = $this->list();

        self::assertCount(0, $list);
        self::assertSame([], $list->items);
    }

    #[Test]
    public function constructorAcceptsTraversable(): void
    {
        $generator = (function (): \Generator {
            yield $this->node('A');
            yield $this->node('B');
        })();

        $list = $this->list();
        $list = new class ($generator) extends NodeList {};

        self::assertCount(2, $list);
        self::assertSame('A', (string) $list->items[0]);
        self::assertSame('B', (string) $list->items[1]);
    }

    #[Test]
    public function constructorReindexesNonListArrays(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');

        $list = new class ([7 => $a, 42 => $b]) extends NodeList {};

        self::assertSame([$a, $b], $list->items);
    }

    #[Test]
    public function firstAndLastAreNullWhenEmpty(): void
    {
        $list = $this->list();

        self::assertNull($list->first());
        self::assertNull($list->last());
        self::assertNull($list->first());
        self::assertNull($list->last());
    }

    #[Test]
    public function firstAndLastReturnBoundaryNodes(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $c = $this->node('C');
        $list = $this->list([$a, $b, $c]);

        self::assertSame($a, $list->first());
        self::assertSame($c, $list->last());
    }

    #[Test]
    public function firstAndLastAreTheSameNodeInSingleItemList(): void
    {
        $a = $this->node('A');
        $list = $this->list([$a]);

        self::assertSame($a, $list->first());
        self::assertSame($a, $list->last());
    }

    #[Test]
    public function virtualPropertiesReflectListMutations(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a]);

        $list->items[] = $b;

        self::assertSame($b, $list->last(), 'The $last property must be recalculated on each access');
    }

    #[Test]
    public function findIndexReturnsPositionOfNode(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a, $b]);

        self::assertSame(0, $list->findIndex($a));
        self::assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullForUnknownNode(): void
    {
        $list = $this->list([$this->node('A')]);

        self::assertNull($list->findIndex($this->node('B')));
    }

    #[Test]
    public function findIndexComparesByIdentityNotByValue(): void
    {
        $list = $this->list([$this->node('A')]);

        self::assertNull($list->findIndex($this->node('A')));
    }

    #[Test]
    public function offsetExistsReflectsListContent(): void
    {
        $list = $this->list([$this->node('A')]);

        self::assertTrue($list->offsetExists(0));
        self::assertFalse($list->offsetExists(1));
        self::assertTrue(isset($list[0]));
        self::assertFalse(isset($list[1]));
    }

    #[Test]
    public function offsetGetReturnsNodeOrNull(): void
    {
        $a = $this->node('A');
        $list = $this->list([$a]);

        self::assertSame($a, $list[0]);
        self::assertNull($list[1]);
    }

    #[Test]
    public function offsetSetReplacesExistingNode(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a]);

        $list[0] = $b;

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function offsetSetKeepsListIndexedSequentially(): void
    {
        $list = $this->list([$this->node('A')]);

        $list[5] = $this->node('B');

        self::assertSame([0, 1], \array_keys($list->items));
    }

    #[Test]
    public function offsetSetWithoutIndexAppendsNode(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a]);

        $list[] = $b;

        self::assertCount(2, $list);
        self::assertSame($b, $list->last());
    }

    #[Test]
    public function offsetUnsetRemovesNodeAndReindexes(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $c = $this->node('C');
        $list = $this->list([$a, $b, $c]);

        unset($list[1]);

        self::assertCount(2, $list);
        self::assertSame([$a, $c], $list->items);
    }

    #[Test]
    public function offsetUnsetOfUnknownIndexKeepsListUnchanged(): void
    {
        $a = $this->node('A');
        $list = $this->list([$a]);

        unset($list[42]);

        self::assertSame([$a], $list->items);
    }

    #[Test]
    public function iteratorYieldsAllNodesInOrder(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($list->getIterator()));
    }

    #[Test]
    public function iteratorOfEmptyListYieldsNothing(): void
    {
        self::assertSame([], \iterator_to_array($this->list()->getIterator()));
    }

    #[Test]
    public function countReflectsNumberOfNodes(): void
    {
        self::assertCount(0, $this->list());
        self::assertCount(1, $this->list([$this->node('A')]));
        self::assertCount(2, $this->list([$this->node('A'), $this->node('B')]));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        self::assertSame(0, $this->list()->offset);
    }

    #[Test]
    public function offsetGetIsAnAliasOfTheArrayAccess(): void
    {
        $a = $this->node('A');
        $list = $this->list([$a]);

        self::assertSame($a, $list->offsetGet(0));
        self::assertNull($list->offsetGet(1));
    }

    #[Test]
    public function offsetSetIsAnAliasOfTheArrayAccess(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a]);

        $list->offsetSet(0, $b);

        self::assertSame([$b], $list->items);
    }

    #[Test]
    public function offsetUnsetIsAnAliasOfTheArrayAccess(): void
    {
        $a = $this->node('A');
        $b = $this->node('B');
        $list = $this->list([$a, $b]);

        $list->offsetUnset(0);

        self::assertSame([$b], $list->items);
    }
}
