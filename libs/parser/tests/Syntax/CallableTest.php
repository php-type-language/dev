<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for callable types.
 *
 * @see \TypeLang\Type\CallableTypeNode
 * @see \TypeLang\Type\Callable\CallableParameterNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class CallableTest extends SyntaxTestCase
{
    public function testCallableWithoutParametersAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
            AST, $this->parseAndPrint('foo()'));
    }

    public function testCallableWithParameterAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
              Type\NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('foo(T): void'));
    }

    public function testComplexNestedCallable(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(a)
                Identifier(a)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(int)
                      Identifier(int)
                    Type\Template\TemplateArgumentsListNode
                      Type\Template\TemplateArgumentNode
                        Type\Literal\IntLiteralNode(0)
                      Type\Template\TemplateArgumentNode
                        Type\NamedTypeNode
                          Name(max)
                            Identifier(max)
                Type\Callable\CallableParameterNode(simple)
                  Type\CallableTypeNode
                    Name(c)
                      Identifier(c)
                    Type\Callable\CallableParametersListNode
                      Type\Callable\CallableParameterNode(simple)
                        Type\NullableTypeNode
                          Type\NamedTypeNode
                            Name(C)
                              Identifier(C)
                    Type\NamedTypeNode
                      Name(mixed)
                        Identifier(mixed)
              Type\NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('a(int<0, max>, c(?C): mixed): void'));
    }

    public function testNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Type\Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T $name)'));
    }

    public function testMixedNamedAndAnonymousParameters(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(A)
                      Identifier(A)
                  Type\Literal\VariableLiteralNode($a)
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(B)
                      Identifier(B)
                Type\Callable\CallableParameterNode(simple)
                  Type\NamedTypeNode
                    Name(C)
                      Identifier(C)
            AST, $this->parseAndPrint('foo(A $a, B, C)'));
    }

    public function testOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(output)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T&)'));
    }

    public function testOutputNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(output)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Type\Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T &$name)'));
    }

    public function testOptionalParameter(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(optional)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T=)'));
    }

    public function testVariadicParameterPrefixSyntax(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(variadic)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(...T)'));
    }

    public function testVariadicParameterPostfixSyntax(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(variadic)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T...)'));
    }

    public function testVariadicNamedOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            Type\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Type\Callable\CallableParametersListNode
                Type\Callable\CallableParameterNode(output, variadic)
                  Type\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Type\Literal\VariableLiteralNode($name)
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
