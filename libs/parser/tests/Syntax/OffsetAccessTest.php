<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the type offset access syntax (e.g. "T['offset']").
 *
 * @see \TypeLang\Node\Type\TypeOffsetAccessNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class OffsetAccessTest extends SyntaxTestCase
{
    public function testStringOffset(): void
    {
        self::assertSame(<<<'AST'
            Type\TypeOffsetAccessNode
              Type\Literal\StringLiteralNode('offset')
              Type\NamedTypeNode
                Name(T)
                  Identifier(T)
            AST, $this->parseAndPrint("T['offset']"));
    }

    public function testDependentKeyOffset(): void
    {
        self::assertSame(<<<'AST'
            Type\TypeOffsetAccessNode
              Type\NamedTypeNode
                Name(U)
                  Identifier(U)
              Type\NamedTypeNode
                Name(T)
                  Identifier(T)
            AST, $this->parseAndPrint('T[U]'));
    }

    public function testShapeWithNumericOffset(): void
    {
        self::assertSame(<<<'AST'
            Type\TypeOffsetAccessNode
              Type\Literal\IntLiteralNode(0)
              Type\NamedTypeNode
                Name(array)
                  Identifier(array)
                Type\Shape\FieldsListNode(sealed)
                  Type\Shape\ImplicitFieldNode(required)
                    Type\NamedTypeNode
                      Name(int)
                        Identifier(int)
                  Type\Shape\ImplicitFieldNode(required)
                    Type\NamedTypeNode
                      Name(string)
                        Identifier(string)
            AST, $this->parseAndPrint('array{int, string}[0]'));
    }

    public function testComplexOffsetWithGenericsAndShapes(): void
    {
        self::assertSame(<<<'AST'
            Type\TypeOffsetAccessNode
              Type\NamedTypeNode
                Name(object)
                  Identifier(object)
                Type\Shape\FieldsListNode(unsealed)
                  Type\Shape\NamedFieldNode(required)
                    Identifier(key)
                    Type\NamedTypeNode
                      Name(int)
                        Identifier(int)
              Type\NamedTypeNode
                Name(T)
                  Identifier(T)
                Type\Template\TemplateArgumentsListNode
                  Type\Template\TemplateArgumentNode
                    Type\NamedTypeNode
                      Name(U)
                        Identifier(U)
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
