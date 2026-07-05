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

        self::assertCount(0, $list);
        self::assertNull($list->first);
        self::assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsParameters(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        self::assertCount(2, $list);
        self::assertSame($a, $list->first);
        self::assertSame($b, $list->last);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        self::assertSame(0, $list->findIndex($a));
        self::assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullWhenNotFound(): void
    {
        $list = new CallableParameterListNode([$this->makeParam('int')]);
        $absent = $this->makeParam('string');

        self::assertNull($list->findIndex($absent));
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function iteratorYieldsParameters(): void
    {
        $a = $this->makeParam('string');
        $b = $this->makeParam('int');
        $list = new CallableParameterListNode([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new CallableParameterListNode();

        self::assertSame(0, $list->offset);
    }
}
