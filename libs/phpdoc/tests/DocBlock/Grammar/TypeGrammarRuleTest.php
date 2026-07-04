<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Grammar;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\TypeParser;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Tests\TestCase;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NullableTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\UnionTypeNode;

final class TypeGrammarRuleTest extends TestCase
{
    private function rule(): TypeGrammarRule
    {
        return new TypeGrammarRule(new TypeParser());
    }

    #[Test]
    public function producesANamedType(): void
    {
        $type = $this->rule()(new Cursor('array<int, string>'));

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('array', (string) $type->name);
    }

    #[Test]
    public function producesANullableType(): void
    {
        $type = $this->rule()(new Cursor('?string'));

        self::assertInstanceOf(NullableTypeNode::class, $type);
    }

    #[Test]
    public function producesAUnionType(): void
    {
        $type = $this->rule()(new Cursor('int|string'));

        self::assertInstanceOf(UnionTypeNode::class, $type);
    }

    /**
     * Only the type is consumed, the trailing description stays for the next
     * rule.
     */
    #[Test]
    public function stopsAfterTheType(): void
    {
        $cursor = new Cursor('int|string and the rest');
        $type = $this->rule()($cursor);

        self::assertInstanceOf(UnionTypeNode::class, $type);
        self::assertSame(11, $cursor->offset);
    }

    /**
     * The consumed offset is rebased onto the source when the cursor does not
     * start at zero.
     */
    #[Test]
    public function respectsTheCursorBase(): void
    {
        $cursor = new Cursor('int rest', base: 100);
        $type = $this->rule()($cursor);

        self::assertInstanceOf(TypeNode::class, $type);
        self::assertSame(104, $cursor->offset);
    }

    #[Test]
    public function rejectsAnEmptyInput(): void
    {
        $this->expectException(NoMatchException::class);

        $this->rule()(new Cursor(''));
    }
}
