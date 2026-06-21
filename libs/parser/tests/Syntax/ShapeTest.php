<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for shape (structural) types.
 *
 * @see \TypeLang\Type\Shape\FieldsListNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class ShapeTest extends SyntaxTestCase
{
    public function testNamedExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(required)
                  Identifier(a)
                  Type\NamedTypeNode
                    Name(first)
                      Identifier(first)
                Type\Shape\NamedFieldNode(required)
                  Identifier(b)
                  Type\NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{a: first, b: second}'));
    }

    public function testNumericExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NumericFieldNode(required)
                  Type\Literal\IntLiteralNode(1)
                  Type\NamedTypeNode
                    Name(first)
                      Identifier(first)
                Type\Shape\NumericFieldNode(required)
                  Type\Literal\IntLiteralNode(42)
                  Type\NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{1: first, 42: second}'));
    }

    public function testStringExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\StringNamedFieldNode(required)
                  Type\Literal\StringLiteralNode("name-some")
                  Type\NamedTypeNode
                    Name(first)
                      Identifier(first)
                Type\Shape\StringNamedFieldNode(required)
                  Type\Literal\StringLiteralNode("escape\nchars")
                  Type\NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{"name-some": first, "escape\\nchars": second}'));
    }

    public function testImplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\ImplicitFieldNode(required)
                  Type\NamedTypeNode
                    Name(first)
                      Identifier(first)
                Type\Shape\ImplicitFieldNode(required)
                  Type\NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(required)
                  Identifier(a)
                  Type\NamedTypeNode
                    Name(int)
                      Identifier(int)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(optional)
                  Identifier(key)
                  Type\NamedTypeNode
                    Name(Type)
                      Identifier(Type)
            AST, $this->parseAndPrint('array{key?: Type}'));
    }

    public function testUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Shape\FieldsListNode(unsealed)
                Type\Shape\NamedFieldNode(required)
                  Identifier(key)
                  Type\NamedTypeNode
                    Name(type)
                      Identifier(type)
            AST, $this->parseAndPrint('array{key: type, ...}'));
    }

    public function testTypedUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(array)
                Identifier(array)
              Type\Template\TemplateArgumentsListNode
                Type\Template\TemplateArgumentNode
                  Type\NamedTypeNode
                    Name(string)
                      Identifier(string)
                Type\Template\TemplateArgumentNode
                  Type\NamedTypeNode
                    Name(object)
                      Identifier(object)
              Type\Shape\FieldsListNode(unsealed)
                Type\Shape\NamedFieldNode(required)
                  Identifier(user)
                  Type\NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('array{user: User, ...<string, object>}'));
    }

    public function testShapeOnArbitraryTypeName(): void
    {
        self::assertSame(<<<'AST'
            Type\NamedTypeNode
              Name(App\Domain\User)
                Identifier(App)
                Identifier(Domain)
                Identifier(User)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(required)
                  Identifier(userName)
                  Type\NamedTypeNode
                    Name(non-empty-string)
                      Identifier(non-empty-string)
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
