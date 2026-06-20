<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the type offset access syntax (e.g. "T['offset']").
 *
 * @see \TypeLang\Parser\Node\Stmt\TypeOffsetAccessNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class OffsetAccessTest extends SyntaxTestCase
{
    public function testStringOffset(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypeOffsetAccessNode
              Stmt\NamedTypeNode
                Name(T)
                  Identifier(T)
              Literal\StringLiteralNode('offset')
            AST, $this->parseAndPrint("T['offset']"));
    }

    public function testDependentKeyOffset(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypeOffsetAccessNode
              Stmt\NamedTypeNode
                Name(T)
                  Identifier(T)
              Stmt\NamedTypeNode
                Name(U)
                  Identifier(U)
            AST, $this->parseAndPrint('T[U]'));
    }

    public function testShapeWithNumericOffset(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypeOffsetAccessNode
              Stmt\NamedTypeNode
                Name(array)
                  Identifier(array)
                Stmt\Shape\FieldsListNode(sealed)
                  Stmt\Shape\ImplicitFieldNode(required)
                    Stmt\NamedTypeNode
                      Name(int)
                        Identifier(int)
                  Stmt\Shape\ImplicitFieldNode(required)
                    Stmt\NamedTypeNode
                      Name(string)
                        Identifier(string)
              Literal\IntLiteralNode(0)
            AST, $this->parseAndPrint('array{int, string}[0]'));
    }

    public function testComplexOffsetWithGenericsAndShapes(): void
    {
        self::assertSame(<<<'AST'
            Stmt\TypeOffsetAccessNode
              Stmt\NamedTypeNode
                Name(T)
                  Identifier(T)
                Stmt\Template\TemplateArgumentsListNode
                  Stmt\Template\TemplateArgumentNode
                    Stmt\NamedTypeNode
                      Name(U)
                        Identifier(U)
              Stmt\NamedTypeNode
                Name(object)
                  Identifier(object)
                Stmt\Shape\FieldsListNode(unsealed)
                  Stmt\Shape\NamedFieldNode(required)
                    Stmt\NamedTypeNode
                      Name(int)
                        Identifier(int)
                    Identifier(key)
            AST, $this->parseAndPrint('T<U>[object{key: int, ...}]'));
    }

    public function testOffsetCannotBeDoubleBracketed(): void
    {
        $this->expectParsingException('unexpected "["');

        $this->parse('Collection[[Some]]');
    }

    public function testTypeMustPrecedeOffset(): void
    {
        $this->expectParsingException('unexpected "{"');

        $this->parse("Collection['key']{key: string}");
    }
}
