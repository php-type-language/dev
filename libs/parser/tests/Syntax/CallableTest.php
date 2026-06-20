<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for callable types.
 *
 * @see \TypeLang\Parser\Node\Stmt\CallableTypeNode
 * @see \TypeLang\Parser\Node\Stmt\Callable\CallableParameterNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class CallableTest extends SyntaxTestCase
{
    public function testCallableWithoutParametersAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
            AST, $this->parseAndPrint('foo()'));
    }

    public function testCallableWithParameterAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
              Stmt\NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('foo(T): void'));
    }

    public function testComplexNestedCallable(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(a)
                Identifier(a)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(simple)
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
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\CallableTypeNode
                    Name(c)
                      Identifier(c)
                    Stmt\Callable\CallableParametersListNode
                      Stmt\Callable\CallableParameterNode(simple)
                        Stmt\NullableTypeNode
                          Stmt\NamedTypeNode
                            Name(C)
                              Identifier(C)
                    Stmt\NamedTypeNode
                      Name(mixed)
                        Identifier(mixed)
              Stmt\NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('a(int<0, max>, c(?C): mixed): void'));
    }

    public function testNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T $name)'));
    }

    public function testMixedNamedAndAnonymousParameters(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\NamedTypeNode
                    Name(A)
                      Identifier(A)
                  Literal\VariableLiteralNode($a)
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\NamedTypeNode
                    Name(B)
                      Identifier(B)
                Stmt\Callable\CallableParameterNode(simple)
                  Stmt\NamedTypeNode
                    Name(C)
                      Identifier(C)
            AST, $this->parseAndPrint('foo(A $a, B, C)'));
    }

    public function testOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(output)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T&)'));
    }

    public function testOutputNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(output)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T &$name)'));
    }

    public function testOptionalParameter(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(optional)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T=)'));
    }

    public function testVariadicParameterPrefixSyntax(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(variadic)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(...T)'));
    }

    public function testVariadicParameterPostfixSyntax(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(variadic)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T...)'));
    }

    public function testVariadicNamedOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
                Stmt\Callable\CallableParameterNode(output, variadic)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(...T &$name)'));
    }

    public function testParameterWithoutTypeIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "$name"');

        $this->parse('foo(T= $name)');
    }

    public function testAmpersandMustFollowParameterType(): void
    {
        $this->expectParsingException('unexpected "T"');

        $this->parse('foo(&T)');
    }

    public function testVariadicCannotBeBothPrefixAndPostfix(): void
    {
        $this->expectParsingException('Either prefix or postfix variadic syntax should be used, but not both');

        $this->parse('foo(...T...)');
    }

    public function testVariadicParameterCannotHaveDefault(): void
    {
        $this->expectParsingException('Cannot have variadic param with a default');

        $this->parse('foo(T ...$name=)');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected ","');

        $this->parse('foo(,T)');
    }
}
