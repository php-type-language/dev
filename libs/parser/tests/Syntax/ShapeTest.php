<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for shape (structural) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ShapeTest extends SyntaxTestCase
{
    public function testNamedExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(first)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(b)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{a: first, b: second}'));
    }

    public function testNumericExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NumericFieldNode(isOptional=false)
                  Literal\IntLiteralNode(1)
                  NamedTypeNode
                    Name(first)
                Shape\NumericFieldNode(isOptional=false)
                  Literal\IntLiteralNode(42)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{1: first, 42: second}'));
    }

    public function testStringExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\StringNamedFieldNode(isOptional=false)
                  Literal\StringLiteralNode("name-some")
                  NamedTypeNode
                    Name(first)
                Shape\StringNamedFieldNode(isOptional=false)
                  Literal\StringLiteralNode("escape\nchars")
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{"name-some": first, "escape\\nchars": second}'));
    }

    public function testImplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\ImplicitFieldNode(isOptional=false)
                  NamedTypeNode
                    Name(first)
                Shape\ImplicitFieldNode(isOptional=false)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=true)
                  Identifier(key)
                  NamedTypeNode
                    Name(Type)
            AST, $this->parseAndPrint('array{key?: Type}'));
    }

    public function testUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=false)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(key)
                  NamedTypeNode
                    Name(type)
            AST, $this->parseAndPrint('array{key: type, ...}'));
    }

    public function testTypedUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(string)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(object)
              Shape\FieldsListNode(isSealed=false)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(user)
                  NamedTypeNode
                    Name(User)
            AST, $this->parseAndPrint('array{user: User, ...<string, object>}'));
    }

    public function testShapeOnArbitraryTypeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(App\Domain\User)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
            AST, $this->parseAndPrint('App\\Domain\\User{userName: non-empty-string}'));
    }

    public function testCannotMixExplicitAndImplicitKeys(): void
    {
        $this->expectParsingException('Cannot mix explicit and implicit shape keys');

        $this->parse('array{named: first, second}');
    }

    public function testDuplicateKeyIsNotAllowed(): void
    {
        $this->expectParsingException('Duplicate key "a"');

        $this->parse('array{a: int, a: string}');
    }

    public function testOptionalValueSyntaxIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "?"');

        $this->parse('array{key: Type?}');
    }
}
