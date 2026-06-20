<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for generic (template argument) grammar and call-site hints.
 *
 * @see \TypeLang\Parser\Node\Stmt\Template\TemplateArgumentsListNode
 * @see \TypeLang\Parser\Node\Stmt\Template\TemplateArgumentNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class GenericTest extends SyntaxTestCase
{
    public function testTemplateArguments(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(Path\To\ExampleClass)
                Identifier(Path)
                Identifier(To)
                Identifier(ExampleClass)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
            AST, $this->parseAndPrint('Path\\To\\ExampleClass<T, U>'));
    }

    public function testSingleTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(Collection)
                Identifier(Collection)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('Collection<User>'));
    }

    public function testNestedTemplateArguments(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(iterable)
                Identifier(iterable)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                    Stmt\Template\TemplateArgumentsListNode
                      Stmt\Template\TemplateArgumentNode
                        Literal\IntLiteralNode(0)
                      Stmt\Template\TemplateArgumentNode
                        Stmt\NamedTypeNode
                          Name(max)
                            Identifier(max)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(Collection)
                      Identifier(Collection)
                    Stmt\Template\TemplateArgumentsListNode
                      Stmt\Template\TemplateArgumentNode
                        Stmt\NamedTypeNode
                          Name(User)
                            Identifier(User)
            AST, $this->parseAndPrint('iterable<int<0, max>, Collection<User>>'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(Request)
                      Identifier(Request)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('HashMap<Request, User,>'));
    }

    public function testCallSiteHint(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(array-key)
                      Identifier(array-key)
                Stmt\Template\TemplateArgumentNode
                  Identifier(covariant)
                  Stmt\NamedTypeNode
                    Name(Request)
                      Identifier(Request)
            AST, $this->parseAndPrint('HashMap<array-key, covariant Request>'));
    }

    public function testMissingTemplateArgument(): void
    {
        $this->expectParsingException('unexpected ">"');

        $this->parse('example<>');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected ","');

        $this->parse('example<,T>');
    }

    public function testHintAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "User"');

        $this->parse('Collection<42 User>');
    }

    public function testMultipleHintsAreNotAllowed(): void
    {
        $this->expectParsingException('unexpected "Request"');

        $this->parse('HashMap<array-key, some covariant Request>');
    }
}
