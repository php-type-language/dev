<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateArgumentListNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Tests\TestCase;

final class TemplateArgumentsListNodeTest extends TestCase
{
    private function makeArg(string $type): TemplateArgumentNode
    {
        return new TemplateArgumentNode(new NamedTypeNode(Name::createFromString($type)));
    }

    #[Test]
    public function emptyListByDefault(): void
    {
        $list = new TemplateArgumentListNode();

        self::assertCount(0, $list);
        self::assertNull($list->first);
        self::assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsArguments(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        self::assertCount(2, $list);
        self::assertSame($a, $list->first);
        self::assertSame($b, $list->last);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        self::assertSame(0, $list->findIndex($a));
        self::assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullForAbsentNode(): void
    {
        $list = new TemplateArgumentListNode([$this->makeArg('int')]);

        self::assertNull($list->findIndex($this->makeArg('string')));
    }

    #[Test]
    public function arrayAccessOffsetGet(): void
    {
        $a = $this->makeArg('string');
        $list = new TemplateArgumentListNode([$a]);

        self::assertSame($a, $list[0]);
        self::assertNull($list[1]);
    }

    #[Test]
    public function arrayAccessOffsetExists(): void
    {
        $list = new TemplateArgumentListNode([$this->makeArg('int')]);

        self::assertTrue(isset($list[0]));
        self::assertFalse(isset($list[1]));
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function iteratorYieldsArguments(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new TemplateArgumentListNode();

        self::assertSame(0, $list->offset);
    }
}
