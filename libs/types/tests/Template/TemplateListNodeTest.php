<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Tests\TestCase;

final class TemplateListNodeTest extends TestCase
{
    /**
     * @param non-empty-string $type
     */
    private function makeArg(string $type): TypedTemplateArgumentNode
    {
        return new TypedTemplateArgumentNode(new NamedTypeNode(new Name($type)));
    }

    #[Test]
    public function emptyListIsNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TemplateListNode([]);
    }

    #[Test]
    public function arrayAccessOffsetUnsetReindexes(): void
    {
        $a = $this->makeArg('string');
        $b = $this->makeArg('int');
        $list = new TemplateListNode([$a, $b]);
        unset($list[0]);

        self::assertCount(1, $list);
        self::assertSame($b, $list[0]);
    }

    #[Test]
    public function removingTheLastItemIsRejected(): void
    {
        $list = new TemplateListNode([$this->makeArg('string')]);

        $this->expectException(\UnderflowException::class);

        unset($list[0]);
    }

    #[Test]
    public function rejectedRemovalLeavesTheListAsItIs(): void
    {
        $list = new TemplateListNode([$argument = $this->makeArg('string')]);

        try {
            unset($list[0]);

            self::fail('The item must not be removed');
        } catch (\UnderflowException) {
            self::assertSame([$argument], $list->children);
        }
    }
}
