<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\Node;
use TypeLang\Type\SymbolNode;
use TypeLang\Type\VariableNode;

#[CoversClass(SymbolNode::class)]
final class SymbolNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{SymbolNode, non-empty-string}>
     */
    public static function nodeDataProvider(): iterable
    {
        yield 'an identifier' => [new Identifier('Foo'), 'Foo'];
        yield 'a simple name' => [new Name('Foo'), 'Foo'];
        yield 'a qualified name' => [new Name('Any', new NamespaceName('Some')), 'Some\Any'];
        yield 'a variable' => [new VariableNode('foo'), '$foo'];
    }

    #[Test]
    #[DataProvider('nodeDataProvider')]
    public function isANodeAndStringable(SymbolNode $node): void
    {
        self::assertInstanceOf(Node::class, $node);
        self::assertInstanceOf(\Stringable::class, $node);
    }

    #[Test]
    #[DataProvider('nodeDataProvider')]
    public function stringFormIsTheWrittenText(SymbolNode $node, string $text): void
    {
        self::assertSame($text, $node->toString());
        self::assertSame($text, (string) $node);
    }

    #[Test]
    #[DataProvider('nodeDataProvider')]
    public function lowerStringFormIsTheWrittenTextInLowerCase(SymbolNode $node, string $text): void
    {
        self::assertSame(\strtolower($text), $node->toLowerString());
    }

    /**
     * @return iterable<non-empty-string, array{SymbolNode, bool}>
     */
    public static function specialDataProvider(): iterable
    {
        yield 'self' => [new Identifier('self'), true];
        yield 'parent in upper case' => [new Name('PARENT'), true];
        yield 'static' => [new Identifier('static'), true];
        yield 'a regular word' => [new Identifier('Foo'), false];
        yield 'a built-in word' => [new Name('int'), false];
        yield 'a qualified special word' => [new Name('self', new NamespaceName('Foo')), false];
        yield 'a variable of a special word' => [new VariableNode('self'), false];
    }

    #[Test]
    #[DataProvider('specialDataProvider')]
    public function specialWordsAreRecognized(SymbolNode $node, bool $expected): void
    {
        self::assertSame($expected, $node->isSpecial());
    }

    /**
     * @return iterable<non-empty-string, array{SymbolNode, bool}>
     */
    public static function virtualDataProvider(): iterable
    {
        yield 'a regular identifier' => [new Identifier('Foo'), false];
        yield 'a hyphenated identifier' => [new Identifier('non-empty-string'), true];
        yield 'a regular name' => [new Name('Any', new NamespaceName('Some')), false];
        yield 'a hyphenated name' => [new Name('non-empty-string'), true];
        yield 'a name with a hyphenated namespace' => [new Name('Any', new NamespaceName('non-empty')), true];
        yield 'a variable' => [new VariableNode('foo'), false];
    }

    #[Test]
    #[DataProvider('virtualDataProvider')]
    public function virtualWordsAreRecognized(SymbolNode $node, bool $expected): void
    {
        self::assertSame($expected, $node->isVirtual());
    }

    /**
     * @return iterable<non-empty-string, array{SymbolNode, bool}>
     */
    public static function builtinDataProvider(): iterable
    {
        foreach (['mixed', 'string', 'int', 'float', 'bool', 'object', 'array', 'void', 'never', 'callable', 'iterable', 'null', 'true', 'false'] as $word) {
            yield $word . ' as an identifier' => [new Identifier($word), true];
            yield $word . ' as a name' => [new Name($word), true];
        }

        yield 'in upper case' => [new Name('INT'), true];
        yield 'a regular word' => [new Identifier('Foo'), false];
        yield 'a special word' => [new Name('self'), false];
        yield 'a qualified built-in word' => [new Name('int', new NamespaceName('Foo')), false];
        yield 'a fully qualified built-in word' => [new Name('int', new NamespaceName('', NamespaceName\NamespaceKind::FullyQualified)), false];
        yield 'a variable of a built-in word' => [new VariableNode('int'), false];
    }

    #[Test]
    #[DataProvider('builtinDataProvider')]
    public function builtinWordsAreRecognized(SymbolNode $node, bool $expected): void
    {
        self::assertSame($expected, $node->isBuiltin());
    }
}
