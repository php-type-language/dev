<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the input a type cannot be read out of, that is, the one that is
 * empty, the one that is cut short and the one that is punctuation alone.
 */
#[Group('unit'), Group('type-lang/parser')]
final class MalformedInputTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function emptyInputDataProvider(): iterable
    {
        yield 'nothing at all' => [''];
        yield 'spaces' => ['   '];
        yield 'a line terminator' => ["\n"];
        yield 'a tabulation' => ["\t"];
    }

    /**
     * @throws \Throwable
     */
    #[DataProvider('emptyInputDataProvider')]
    public function testAnEmptyInputIsNoType(string $type): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse($type);
    }

    /**
     * A type that is cut short ends where the input does, so the reading
     * stops at the end of it rather than at a token.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function truncatedInputDataProvider(): iterable
    {
        yield 'a dangling union' => ['int|'];
        yield 'a dangling intersection' => ['int&'];
        yield 'a question mark alone' => ['?'];
        yield 'an unclosed shape' => ['array{'];
        yield 'an unclosed field' => ['array{a:'];
        yield 'an unclosed argument list' => ['Some<'];
        yield 'an unclosed parameter list' => ['callable('];
        yield 'an unclosed offset' => ['int['];
        yield 'an unclosed group' => ['(int'];
        yield 'a name that is a separator short' => ['Some\\'];
        yield 'a class constant that is a name short' => ['Some::'];
        yield 'a condition without its branches' => ['A is B ?'];
        yield 'a condition of a single branch' => ['A is B ? C'];
        yield 'a condition without its comparand' => ['A is'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('truncatedInputDataProvider')]
    public function testATruncatedTypeIsRefused(string $type): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse($type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function strayTokenDataProvider(): iterable
    {
        yield 'a union delimiter' => ['|', 'unexpected "|"'];
        yield 'an intersection delimiter' => ['&', 'unexpected "&"'];
        yield 'a comma' => [',', 'unexpected ","'];
        yield 'a colon' => [':', 'unexpected ":"'];
        yield 'a double colon' => ['::CONST', 'unexpected "::"'];
        yield 'a closing brace' => ['}', 'unexpected "}"'];
        yield 'a closing bracket' => [']', 'unexpected "]"'];
        yield 'a closing parenthesis' => [')', 'unexpected ")"'];
        yield 'an ellipsis' => ['...', 'unexpected "..."'];
        yield 'an assignment' => ['=', 'unexpected "="'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('strayTokenDataProvider')]
    public function testPunctuationAloneIsNoType(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function trailingTokenDataProvider(): iterable
    {
        yield 'a question mark behind a type' => ['int?', 'unexpected "?"'];
        yield 'an angle bracket too many' => ['Some<int>>', 'unexpected ">"'];
        yield 'a brace too many' => ['array{a: int}}', 'unexpected "}"'];
        yield 'a bracket too many' => ['int[]]', 'unexpected "]"'];
        yield 'an empty argument list' => ['Some<>', 'unexpected ">"'];
        yield 'an empty group' => ['()', 'unexpected ")"'];
        yield 'a constant of a generic type' => ['Some<T>::CONST', 'unexpected "::"'];
        yield 'a second type' => ['int string', 'unexpected "string"'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $message
     * @throws \Throwable
     */
    #[DataProvider('trailingTokenDataProvider')]
    public function testATokenThatFollowsAWholeTypeIsRefused(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    /**
     * A tolerant reading keeps whatever type it has read and says where it
     * stopped, rather than refusing the input whole.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, int<0, max>}>
     */
    public static function tolerantInputDataProvider(): iterable
    {
        yield 'a name and a tail' => ['int foo bar', 'int', 4];
        yield 'a union and a tail' => ['int|string extra', 'int|string', 11];
        yield 'a shape and a tail' => ['array{a: int} tail', 'array{a: int}', 14];
        yield 'a list and an ellipsis' => ['int[] ...', 'int[]', 6];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $expected
     * @param int<0, max> $offset
     * @throws \Throwable
     */
    #[DataProvider('tolerantInputDataProvider')]
    public function testATolerantReadingStopsAtWhatItCannotRead(
        string $type,
        string $expected,
        int $offset,
    ): void {
        $result = $this->parseTolerant($type);

        self::assertSame($expected, (new \TypeLang\Printer\PrettyTypePrinter())->print($result->type));
        self::assertSame($offset, $result->offset);
    }

    /**
     * A tolerant reading is tolerant of a tail alone, so an input that opens
     * no type at all is refused all the same.
     */
    public function testATolerantReadingRefusesAnInputThatOpensNoType(): void
    {
        $this->expectParsingException();

        $this->parseTolerant('|int');
    }
}
