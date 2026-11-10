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
    public function isANodeAndStringable(SymbolNode $node, string $_text): void
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
}
