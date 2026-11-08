<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the type offset access syntax (e.g. "T['offset']").
 */
#[Group('unit'), Group('type-lang/parser')]
final class OffsetAccessTest extends SyntaxTestCase
{
    public function testStringOffset(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypeOffsetAccessNode
              Literal\StringLiteralNode('offset')
              NamedTypeNode
                Name(T)
            AST, $this->parseAndPrint("T['offset']"));
    }

    public function testDependentKeyOffset(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypeOffsetAccessNode
              NamedTypeNode
                Name(U)
              NamedTypeNode
                Name(T)
            AST, $this->parseAndPrint('T[U]'));
    }

    public function testShapeWithNumericOffset(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypeOffsetAccessNode
              Literal\IntLiteralNode(0)
              NamedTypeNode
                Name(array)
                Shape\FieldsListNode(isSealed=true)
                  Shape\ImplicitFieldNode(isOptional=false)
                    NamedTypeNode
                      Name(int)
                  Shape\ImplicitFieldNode(isOptional=false)
                    NamedTypeNode
                      Name(string)
            AST, $this->parseAndPrint('array{int, string}[0]'));
    }

    public function testComplexOffsetWithGenericsAndShapes(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypeOffsetAccessNode
              NamedTypeNode
                Name(object)
                Shape\FieldsListNode(isSealed=false)
                  Shape\NamedFieldNode(isOptional=false)
                    Name(key)
                    NamedTypeNode
                      Name(int)
              NamedTypeNode
                Name(T)
                Template\TemplateListNode
                  Template\TypedTemplateArgumentNode
                    NamedTypeNode
                      Name(U)
            AST, $this->parseAndPrint('T<U>[object{key: int, ...}]'));
    }

    public function testOffsetCannotBeDoubleBracketed(): void
    {
        $this->expectParsingException('an offset must be closed with a bracket "]"');

        $this->parse('Collection[[Some]]');
    }

    public function testTypeMustPrecedeOffset(): void
    {
        $this->expectParsingException('unexpected "{"');

        $this->parse("Collection['key']{key: string}");
    }
}
