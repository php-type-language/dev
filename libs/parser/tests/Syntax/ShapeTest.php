<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for shape (structural) types.
 *
 * @see \TypeLang\Parser\Node\Stmt\Shape\FieldsListNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class ShapeTest extends SyntaxTestCase
{
    public function testNamedExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(first)
                      Identifier(first)
                  Identifier(a)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(second)
                      Identifier(second)
                  Identifier(b)
            AST, $this->parseAndPrint('array{a: first, b: second}'));
    }

    public function testNumericExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NumericFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(first)
                      Identifier(first)
                  Literal\IntLiteralNode(1)
                Stmt\Shape\NumericFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(second)
                      Identifier(second)
                  Literal\IntLiteralNode(42)
            AST, $this->parseAndPrint('array{1: first, 42: second}'));
    }

    public function testStringExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\StringNamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(first)
                      Identifier(first)
                  Literal\StringLiteralNode("name-some")
                Stmt\Shape\StringNamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(second)
                      Identifier(second)
                  Literal\StringLiteralNode("escape\nchars")
            AST, $this->parseAndPrint('array{"name-some": first, "escape\\nchars": second}'));
    }

    public function testImplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(first)
                      Identifier(first)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                  Identifier(a)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(optional)
                  Stmt\NamedTypeNode
                    Name(Type)
                      Identifier(Type)
                  Identifier(key)
            AST, $this->parseAndPrint('array{key?: Type}'));
    }

    public function testUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(unsealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(type)
                      Identifier(type)
                  Identifier(key)
            AST, $this->parseAndPrint('array{key: type, ...}'));
    }

    public function testTypedUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(string)
                      Identifier(string)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(object)
                      Identifier(object)
              Stmt\Shape\FieldsListNode(unsealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(User)
                      Identifier(User)
                  Identifier(user)
            AST, $this->parseAndPrint('array{user: User, ...<string, object>}'));
    }

    public function testShapeOnArbitraryTypeName(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(App\Domain\User)
                Identifier(App)
                Identifier(Domain)
                Identifier(User)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(non-empty-string)
                      Identifier(non-empty-string)
                  Identifier(userName)
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
