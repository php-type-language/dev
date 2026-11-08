<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * How `?`, `[]` and `[K]` bind to one another. A tree is written down with
 * every group in parentheses: `?(T[])` is a nullable of a list, `(?T)[]` is
 * a list of nullables, `T[K]` is `(T)[K]`.
 *
 * The order is the one of PHPStan, Psalm, Phan and phpDocumentor: the suffixes
 * bind tighter than the question mark.
 */
final class SuffixPriorityTest extends TestCase
{
    private static function render(TypeNode $node): string
    {
        return match (true) {
            $node instanceof NamedTypeNode => $node->name->toString(),
            $node instanceof ThisTypeNode => '$this',
            $node instanceof NullableTypeNode => '?(' . self::render($node->type) . ')',
            $node instanceof TypesListTypeNode => '(' . self::render($node->type) . ')[]',
            $node instanceof TypeOffsetAccessTypeNode => '('
                . self::render($node->type)
                . ')['
                . self::render($node->access)
                . ']',
            default => $node::class,
        };
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function treeDataProvider(): iterable
    {
        // Lists
        yield 'list' => ['T[]', '(T)[]'];
        yield 'two lists' => ['T[][]', '((T)[])[]'];
        yield 'three lists' => ['T[][][]', '(((T)[])[])[]'];
        yield 'list of this' => ['$this[]', '($this)[]'];
        yield 'list with spaces' => ['T [ ] [ ]', '((T)[])[]'];

        // Offset accesses
        yield 'access' => ['T[U]', '(T)[U]'];
        yield 'two accesses' => ['T[U][V]', '((T)[U])[V]'];
        yield 'three accesses' => ['T[U][V][W]', '(((T)[U])[V])[W]'];
        yield 'access by an access' => ['T[U[V]]', '(T)[(U)[V]]'];
        yield 'access by a list' => ['T[U[]]', '(T)[(U)[]]'];
        yield 'access by an access by a list' => ['T[U[V[]]]', '(T)[(U)[(V)[]]]'];
        yield 'access with spaces' => ['T [ U ]', '(T)[U]'];

        // Lists and accesses together
        yield 'list then access' => ['T[][U]', '((T)[])[U]'];
        yield 'access then list' => ['T[U][]', '((T)[U])[]'];
        yield 'list, access, list' => ['T[][U][]', '(((T)[])[U])[]'];
        yield 'access, list, access' => ['T[U][][V]', '(((T)[U])[])[V]'];
        yield 'list in the key and a list' => ['T[U[]][]', '((T)[(U)[]])[]'];

        // The question mark reaches over the suffixes
        yield 'nullable' => ['?T', '?(T)'];
        yield 'nullable list' => ['?T[]', '?((T)[])'];
        yield 'nullable two lists' => ['?T[][]', '?(((T)[])[])'];
        yield 'nullable access' => ['?T[U]', '?((T)[U])'];
        yield 'nullable two accesses' => ['?T[U][V]', '?(((T)[U])[V])'];
        yield 'nullable list and access' => ['?T[][U]', '?(((T)[])[U])'];
        yield 'nullable access and list' => ['?T[U][]', '?(((T)[U])[])'];

        // Groups turn the order over
        yield 'list of nullables' => ['(?T)[]', '(?(T))[]'];
        yield 'two lists of nullables' => ['(?T)[][]', '((?(T))[])[]'];
        yield 'access of nullables' => ['(?T)[U]', '(?(T))[U]'];
        yield 'nullable of a list in a group' => ['?(T[])', '?((T)[])'];
        yield 'group of a nullable list' => ['(?T[])', '?((T)[])'];
        yield 'nullable of a group and a list' => ['?(T)[]', '?((T)[])'];
        yield 'nullable of a group and an access' => ['?(T)[U]', '?((T)[U])'];
        yield 'nullable of nullable in groups' => ['?(?T)', '?(?(T))'];
        yield 'list of a nullable of a nullable' => ['(?(?T))[]', '(?(?(T)))[]'];
        yield 'group of a nullable and a nullable list' => ['?((?T)[])', '?((?(T))[])'];

        // The question mark in the key
        yield 'nullable key' => ['T[?U]', '(T)[?(U)]'];
        yield 'nullable key of a nullable' => ['?T[?U]', '?((T)[?(U)])'];
        yield 'nullable key with a list' => ['T[?U[]]', '(T)[?((U)[])]'];
        yield 'nullable key with an access' => ['T[?U[V]]', '(T)[?((U)[V])]'];
        yield 'nullable key with a nullable key' => ['T[?U[?V]]', '(T)[?((U)[?(V)])]'];
        yield 'nullable keys' => ['T[?U][?V]', '((T)[?(U)])[?(V)]'];
        yield 'nullable keys of a nullable' => ['?T[?U][?V]', '?(((T)[?(U)])[?(V)])'];
        yield 'nullable key in a group' => ['T[(?U)]', '(T)[?(U)]'];
        yield 'nullable key list in a group' => ['T[(?U)[]]', '(T)[(?(U))[]]'];
        yield 'nullable key then a list' => ['T[?U][]', '((T)[?(U)])[]'];
        yield 'list then a nullable key' => ['T[][?U]', '((T)[])[?(U)]'];
        yield 'nullable of a nullable key in groups' => ['?(T[?U])', '?((T)[?(U)])'];
        yield 'list of nullables with a nullable key' => ['(?T[?U])[]', '(?((T)[?(U)]))[]'];

        // Spaces and trivia do not change the order
        yield 'nullable with a space' => ['? T []', '?((T)[])'];
        yield 'nullable with line breaks' => ["?\nT\n[\n?\nU\n]", '?((T)[?(U)])'];
        yield 'nullable with comments' => ['?/* a */T/* b */[/* c */?/* d */U/* e */]', '?((T)[?(U)])'];
        yield 'group of a nullable with comments' => ['(/* a */?/* b */T/* c */)[]', '(?(T))[]'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $expected
     */
    #[DataProvider('treeDataProvider')]
    public function testSuffixesAndQuestionMarkBindInTheirOrder(string $source, string $expected): void
    {
        self::assertSame($expected, self::render($this->parse($source)));
    }

    /**
     * Two ways to write the same tree.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function equivalentDataProvider(): iterable
    {
        yield 'group around a name' => ['(T)', 'T'];
        yield 'groups around a name' => ['(((T)))', 'T'];
        yield 'group around a list' => ['(T[])', 'T[]'];
        yield 'group around an access' => ['(T[U])', 'T[U]'];
        yield 'group around a nullable' => ['(?T)', '?T'];
        yield 'group around a nullable list' => ['(?T[])', '?T[]'];
        yield 'group around a nullable access' => ['(?T[U])', '?T[U]'];
        yield 'group around a nullable key' => ['(T[?U])', 'T[?U]'];
        yield 'group around a name before the suffix' => ['(T)[]', '(T)[]'];
        yield 'nullable of a group before the suffix' => ['?(T)[]', '?T[]'];
        yield 'nullable of a group of a list' => ['?(T[])', '?T[]'];
        yield 'nullable of a group of an access' => ['?(T[U])', '?T[U]'];
        yield 'group around a key' => ['T[(U)]', 'T[U]'];
        yield 'group around a nullable inside of a key' => ['T[(?U)]', 'T[?U]'];
        yield 'group around a list key' => ['T[(U[])]', 'T[U[]]'];
        yield 'spaces' => ['  ?  T  [  ?  U  ]  ', '?T[?U]'];
        yield 'comments' => ['/* a */?/* b */T/* c */[/* d */?/* e */U/* f */]', '?T[?U]'];
    }

    /**
     * @param non-empty-string $first
     * @param non-empty-string $second
     */
    #[DataProvider('equivalentDataProvider')]
    public function testDifferentWritingsGiveTheSameTree(string $first, string $second): void
    {
        self::assertSame(self::render($this->parse($second)), self::render($this->parse($first)));
    }

    /**
     * Two ways to write trees that are NOT the same, the order is what makes them different.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function differentDataProvider(): iterable
    {
        yield 'nullable list and list of nullables' => ['?T[]', '(?T)[]'];
        yield 'nullable access and access of a nullable' => ['?T[U]', '(?T)[U]'];
        yield 'list then access and access then list' => ['T[][U]', 'T[U][]'];
        yield 'nullable key and nullable access' => ['T[?U]', '?T[U]'];
        yield 'nullable key list and list of nullable keys' => ['T[?U[]]', 'T[?U][]'];
        yield 'nullable of a nullable key and a nullable key' => ['?T[?U]', 'T[?U]'];
    }

    /**
     * @param non-empty-string $first
     * @param non-empty-string $second
     */
    #[DataProvider('differentDataProvider')]
    public function testOrderMakesTheTreesDifferent(string $first, string $second): void
    {
        self::assertNotSame(self::render($this->parse($second)), self::render($this->parse($first)));
    }
}
