<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Grammar;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\DocBlock\Grammar\VariableGrammarRule;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Tests\TestCase;

final class VariableGrammarRuleTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function variableDataProvider(): iterable
    {
        yield 'simple' => ['$var', 'var'];
        yield 'underscore' => ['$_foo123', '_foo123'];
        yield 'unicode' => ['$переменная', 'переменная'];
    }

    #[Test]
    #[DataProvider('variableDataProvider')]
    public function returnsTheNameWithoutTheDollar(string $input, string $expected): void
    {
        $name = new VariableGrammarRule()(new Cursor($input));

        self::assertSame($expected, $name);
    }

    /**
     * Only the variable is consumed, the rest stays for the next rule.
     */
    #[Test]
    public function stopsAtTheFirstWhitespace(): void
    {
        $cursor = new Cursor('$var and the rest');
        $name = new VariableGrammarRule()($cursor);

        self::assertSame('var', $name);
        self::assertSame(4, $cursor->offset);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'whitespace only' => ['   '];
        yield 'missing dollar' => ['var'];
        yield 'dollar only' => ['$'];
        yield 'illegal character' => ['$foo!bar'];
        yield 'namespace separator' => ['$foo\\bar'];
    }

    #[Test]
    #[DataProvider('invalidDataProvider')]
    public function rejectsAnInvalidVariable(string $input): void
    {
        $this->expectException(NoMatchException::class);

        new VariableGrammarRule()(new Cursor($input));
    }
}
