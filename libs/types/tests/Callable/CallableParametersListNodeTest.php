<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Callable;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class CallableParametersListNodeTest extends TestCase
{
    private function makeParam(string $type): CallableParameterNode
    {
        return new CallableParameterNode(type: new NamedTypeNode(Name::createFromString($type)));
    }

    #[Test]
    public function emptyListByDefault(): void
    {
        $list = new CallableParameterListNode();

        $this->assertCount(0, $list);
        $this->assertNull($list->first);
        $this->assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsParameters(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        $this->assertCount(2, $list);
        $this->assertSame($a, $list->first);
        $this->assertSame($b, $list->last);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        $this->assertSame(0, $list->findIndex($a));
        $this->assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullWhenNotFound(): void
    {
        $list = new CallableParameterListNode([$this->makeParam('int')]);
        $absent = $this->makeParam('string');

        $this->assertNull($list->findIndex($absent));
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);
        unset($list[0]);

        $this->assertCount(1, $list);
        $this->assertSame($b, $list[0]);
    }

    #[Test]
    public function iteratorYieldsParameters(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        $this->assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new CallableParameterListNode();

        $this->assertSame(0, $list->offset);
    }
}
