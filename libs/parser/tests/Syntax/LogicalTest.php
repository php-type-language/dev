<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for logical (composite) types: union, intersection, nullable and
 * parentheses grouping.
 *
 * @see \TypeLang\Node\Stmt\UnionTypeNode
 * @see \TypeLang\Node\Stmt\IntersectionTypeNode
 * @see \TypeLang\Node\Stmt\NullableTypeNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class LogicalTest extends SyntaxTestCase
{
    public function testUnionType(): void
    {
        self::assertSame(<<<'AST'
            Stmt\UnionTypeNode
              Stmt\NamedTypeNode
                Name(A)
                  Identifier(A)
              Stmt\NamedTypeNode
                Name(B)
                  Identifier(B)
              Stmt\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('A | B | C'));
    }

    public function testIntersectionType(): void
    {
        self::assertSame(<<<'AST'
            Stmt\IntersectionTypeNode
              Stmt\NamedTypeNode
                Name(A)
                  Identifier(A)
              Stmt\NamedTypeNode
                Name(B)
                  Identifier(B)
              Stmt\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('A & B & C'));
    }

    public function testNullableType(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NullableTypeNode
              Stmt\NamedTypeNode
                Name(Example)
                  Identifier(Example)
            AST, $this->parseAndPrint('?Example'));
    }

    public function testDisjunctiveNormalForm(): void
    {
        self::assertSame(<<<'AST'
            Stmt\UnionTypeNode
              Stmt\IntersectionTypeNode
                Stmt\NamedTypeNode
                  Name(A)
                    Identifier(A)
                Stmt\NamedTypeNode
                  Name(B)
                    Identifier(B)
              Stmt\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('(A & B) | C'));
    }

    public function testConjunctiveNormalForm(): void
    {
        self::assertSame(<<<'AST'
            Stmt\IntersectionTypeNode
              Stmt\UnionTypeNode
                Stmt\NamedTypeNode
                  Name(A)
                    Identifier(A)
                Stmt\NamedTypeNode
                  Name(B)
                    Identifier(B)
              Stmt\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('(A | B) & C'));
    }

    public function testNullableQuestionMarkMustBeBeforeType(): void
    {
        $this->expectParsingException('unexpected "?"');

        $this->parse('Example?');
    }

    public function testDanglingUnionDelimiter(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('int |');
    }

    public function testLeadingUnionDelimiter(): void
    {
        $this->expectParsingException('unexpected "|"');

        $this->parse('| int');
    }

    public function testDanglingIntersectionDelimiter(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('string &');
    }
}
