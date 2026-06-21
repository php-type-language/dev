<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Template\TemplateArgumentListNode;
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

        $this->assertCount(0, $list);
        $this->assertNull($list->first);
        $this->assertNull($list->last);
    }

    #[Test]
    public function constructorAcceptsArguments(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        $this->assertCount(2, $list);
        $this->assertSame($a, $list->first);
        $this->assertSame($b, $list->last);
    }

    #[Test]
    public function findIndexReturnsCorrectPosition(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        $this->assertSame(0, $list->findIndex($a));
        $this->assertSame(1, $list->findIndex($b));
    }

    #[Test]
    public function findIndexReturnsNullForAbsentNode(): void
    {
        $list = new TemplateArgumentListNode([$this->makeArg('int')]);

        $this->assertNull($list->findIndex($this->makeArg('string')));
    }

    #[Test]
    public function arrayAccessOffsetGet(): void
    {
        $a = $this->makeArg('string');
        $list = new TemplateArgumentListNode([$a]);

        $this->assertSame($a, $list[0]);
        $this->assertNull($list[1]);
    }

    #[Test]
    public function arrayAccessOffsetExists(): void
    {
        $list = new TemplateArgumentListNode([$this->makeArg('int')]);

        $this->assertTrue(isset($list[0]));
        $this->assertFalse(isset($list[1]));
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);
        unset($list[0]);

        $this->assertCount(1, $list);
        $this->assertSame($b, $list[0]);
    }

    #[Test]
    public function iteratorYieldsArguments(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateArgumentListNode([$a, $b]);

        $this->assertSame([$a, $b], \iterator_to_array($list));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $list = new TemplateArgumentListNode();

        $this->assertSame(0, $list->offset);
    }
}
