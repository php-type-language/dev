<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the legacy square bracket list syntax (e.g. "T[]").
 */
#[Group('unit'), Group('type-lang/parser')]
final class ListTest extends SyntaxTestCase
{
    public function testSimpleList(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypesListNode
              NamedTypeNode
                Name(User)
            AST, $this->parseAndPrint('User[]'));
    }

    public function testNestedList(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypesListNode
              WrappingTypeNode\TypesListNode
                NamedTypeNode
                  Name(User)
            AST, $this->parseAndPrint('User[][]'));
    }
}
