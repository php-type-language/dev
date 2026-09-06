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
            TypeOffsetAccessNode
              NamedTypeNode
                Name(T)
            AST, $this->parseAndPrint("T['offset']"));
    }

    public function testDependentKeyOffset(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
              NamedTypeNode
                Name(T)
            AST, $this->parseAndPrint('T[U]'));
    }

    public function testShapeWithNumericOffset(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
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
            TypeOffsetAccessNode
              NamedTypeNode
                Name(T)
                Template\TemplateArgumentListNode
                  Template\TemplateArgumentNode
                    NamedTypeNode
                      Name(U)
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
