<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the legacy square bracket list syntax (e.g. "T[]").
 *
 * @see \TypeLang\Node\Stmt\TypesListNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class ListTest extends SyntaxTestCase
{
    public function testSimpleList(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypesListNode
              Stmt\NamedTypeNode
                Name(User)
                  Identifier(User)
            AST, $this->parseAndPrint('User[]'));
    }

    public function testNestedList(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypesListNode
              Stmt\TypesListNode
                Stmt\NamedTypeNode
                  Name(User)
                    Identifier(User)
            AST, $this->parseAndPrint('User[][]'));
    }
}
