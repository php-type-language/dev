<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for logical (composite) types: union, intersection, nullable and
 * parentheses grouping.
 *
 * @see \TypeLang\Type\UnionTypeNode
 * @see \TypeLang\Type\IntersectionTypeNode
 * @see \TypeLang\Type\NullableTypeNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class LogicalTest extends SyntaxTestCase
{
    public function testUnionType(): void
    {
        self::assertSame(<<<'AST'
            Type\UnionTypeNode
              Type\NamedTypeNode
                Name(A)
                  Identifier(A)
              Type\NamedTypeNode
                Name(B)
                  Identifier(B)
              Type\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('A | B | C'));
    }

    public function testIntersectionType(): void
    {
        self::assertSame(<<<'AST'
            Type\IntersectionTypeNode
              Type\NamedTypeNode
                Name(A)
                  Identifier(A)
              Type\NamedTypeNode
                Name(B)
                  Identifier(B)
              Type\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('A & B & C'));
    }

    public function testNullableType(): void
    {
        self::assertSame(<<<'AST'
            Type\NullableTypeNode
              Type\NamedTypeNode
                Name(Example)
                  Identifier(Example)
            AST, $this->parseAndPrint('?Example'));
    }

    public function testDisjunctiveNormalForm(): void
    {
        self::assertSame(<<<'AST'
            Type\UnionTypeNode
              Type\IntersectionTypeNode
                Type\NamedTypeNode
                  Name(A)
                    Identifier(A)
                Type\NamedTypeNode
                  Name(B)
                    Identifier(B)
              Type\NamedTypeNode
                Name(C)
                  Identifier(C)
            AST, $this->parseAndPrint('(A & B) | C'));
    }

    public function testConjunctiveNormalForm(): void
    {
        self::assertSame(<<<'AST'
            Type\IntersectionTypeNode
              Type\UnionTypeNode
                Type\NamedTypeNode
                  Name(A)
                    Identifier(A)
                Type\NamedTypeNode
                  Name(B)
                    Identifier(B)
              Type\NamedTypeNode
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
