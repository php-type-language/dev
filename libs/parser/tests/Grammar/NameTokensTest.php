<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Grammar;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A word and a name with a separator are tokens of their own (grammar/lexemes.pp3).
 */
final class NameTokensTest extends GrammarTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function singleTokenDataProvider(): iterable
    {
        yield 'word' => ['Foo', SyntaxChecker::T_IDENTIFIER];
        yield 'word with hyphens' => ['non-empty-string', SyntaxChecker::T_IDENTIFIER];
        yield 'word with a trailing hyphen' => ['Foo-', SyntaxChecker::T_IDENTIFIER];
        yield 'word with a suffix of a keyword' => ['isFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'word in upper case is not a keyword' => ['IS', SyntaxChecker::T_IDENTIFIER];
        yield 'word of a literal with a suffix' => ['trueFoo', SyntaxChecker::T_IDENTIFIER];
        yield 'multibyte word' => ['Фу', SyntaxChecker::T_IDENTIFIER];

        yield 'qualified' => ['Foo\\Bar', SyntaxChecker::T_NAME];
        yield 'fully qualified' => ['\\Foo', SyntaxChecker::T_NAME];
        yield 'fully qualified and qualified' => ['\\Foo\\Bar\\Baz', SyntaxChecker::T_NAME];
        yield 'qualified with hyphens' => ['Foo-\\Bar-baz', SyntaxChecker::T_NAME];
        yield 'multibyte qualified' => ['Фу\\Бар', SyntaxChecker::T_NAME];
        yield 'keyword leads a namespace' => ['is\\Foo', SyntaxChecker::T_NAME];
        yield 'keyword in a namespace' => ['Foo\\is', SyntaxChecker::T_NAME];
        yield 'fully qualified keyword' => ['\\is', SyntaxChecker::T_NAME];
        yield 'literal word leads a namespace' => ['true\\null', SyntaxChecker::T_NAME];
        yield 'literal word ends a namespace' => ['Foo\\null', SyntaxChecker::T_NAME];
        yield 'fully qualified literal word leads a namespace' => ['\\true\\Foo', SyntaxChecker::T_NAME];

        yield 'keyword is' => ['is', SyntaxChecker::T_IS];
        yield 'keyword not' => ['not', SyntaxChecker::T_NOT];
        yield 'keyword of' => ['of', SyntaxChecker::T_OF];
        yield 'keyword as' => ['as', SyntaxChecker::T_AS];
        yield 'keyword super' => ['super', SyntaxChecker::T_SUPER];

        yield 'true' => ['true', SyntaxChecker::T_TRUE];
        yield 'false in upper case' => ['FALSE', SyntaxChecker::T_FALSE];
        yield 'null' => ['null', SyntaxChecker::T_NULL];
        yield 'fully qualified true' => ['\\true', SyntaxChecker::T_TRUE];
        yield 'fully qualified null' => ['\\NULL', SyntaxChecker::T_NULL];
    }

    #[DataProvider('singleTokenDataProvider')]
    public function testNameIsOneToken(string $source, int $expected): void
    {
        self::assertSame([$expected], (new SyntaxChecker())->tokenize($source));
    }

    /**
     * A name with a separator is never taken for a word of a constant or of a key.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<int>}>
     */
    public static function neighbourTokensDataProvider(): iterable
    {
        $colons = SyntaxChecker::T_DOUBLE_COLON;

        yield 'constant' => ['Foo::BAR', [SyntaxChecker::T_IDENTIFIER, $colons, SyntaxChecker::T_IDENTIFIER]];
        yield 'constant named like a keyword' => ['Foo::is', [SyntaxChecker::T_IDENTIFIER, $colons, SyntaxChecker::T_IS]];
        yield 'constant named like a literal' => ['Foo::NULL', [SyntaxChecker::T_IDENTIFIER, $colons, SyntaxChecker::T_NULL]];
        yield 'constant with a separator' => [
            'Foo::Bar\\Baz',
            [SyntaxChecker::T_IDENTIFIER, $colons, SyntaxChecker::T_NAME],
        ];
        yield 'constant with a leading separator' => [
            'Foo::\\BAR',
            [SyntaxChecker::T_IDENTIFIER, $colons, SyntaxChecker::T_NAME],
        ];
        yield 'class with a namespace' => [
            '\\Foo\\Bar::BAZ',
            [SyntaxChecker::T_NAME, $colons, SyntaxChecker::T_IDENTIFIER],
        ];
        yield 'shape key named like a literal' => [
            'array{true: int}',
            [
                SyntaxChecker::T_IDENTIFIER,
                SyntaxChecker::T_BRACE_OPEN,
                SyntaxChecker::T_TRUE,
                SyntaxChecker::T_COLON,
                SyntaxChecker::T_IDENTIFIER,
                SyntaxChecker::T_BRACE_CLOSE,
            ],
        ];
        yield 'space before a separator' => ['Foo \\Bar', [SyntaxChecker::T_IDENTIFIER, SyntaxChecker::T_NAME]];
        yield 'two words' => ['Foo Bar', [SyntaxChecker::T_IDENTIFIER, SyntaxChecker::T_IDENTIFIER]];
    }

    /**
     * @param list<int> $expected
     */
    #[DataProvider('neighbourTokensDataProvider')]
    public function testNameKeepsToItsKind(string $source, array $expected): void
    {
        self::assertSame($expected, (new SyntaxChecker())->tokenize($source));
    }
}
