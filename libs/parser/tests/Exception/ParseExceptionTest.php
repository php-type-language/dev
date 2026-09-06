<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\Test;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\InternalParseException;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\SemanticParseException;
use TypeLang\Parser\Exception\ShapeFieldDuplicationException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
use TypeLang\Parser\Exception\UnrecognizedTokenException;
use TypeLang\Parser\Tests\TestCase;

final class ParseExceptionTest extends TestCase
{
    #[Test]
    public function everyParseExceptionIsAParserException(): void
    {
        $exception = InternalParseException::becauseTypeStatementIsUnreadable();

        self::assertInstanceOf(ParserExceptionInterface::class, $exception);
        self::assertInstanceOf(\LogicException::class, $exception);
    }

    #[Test]
    public function theUnexpectedTokenIsReportedWithItsLocation(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('foo', 'int|foo', 4);

        self::assertSame(
            'Syntax error, unexpected "foo" in "int|foo" at column 5',
            $exception->getMessage(),
        );
        self::assertSame(ParseException::ERROR_CODE_UNEXPECTED_TOKEN, $exception->getCode());
    }

    #[Test]
    public function theStatementIsOmittedInCaseOfItEqualsTheToken(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('foo', 'foo', 0);

        self::assertSame('Syntax error, unexpected "foo" at column 1', $exception->getMessage());
    }

    #[Test]
    public function theEndOfInputIsReportedInsteadOfANullToken(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected("\0", 'int|', 4);

        self::assertSame(
            'Syntax error, unexpected end of input in "int|" at column 5',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function theEndOfInputIsReportedInsteadOfAnEmptyToken(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('', 'int|', 4);

        self::assertStringContainsString('unexpected end of input', $exception->getMessage());
    }

    #[Test]
    public function theDoubleQuoteTokenIsReportedByItsName(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('"', 'a"', 1);

        self::assertStringContainsString('unexpected double quote (")', $exception->getMessage());
    }

    #[Test]
    public function theMultilineStatementIsReportedUsingTheLineAndColumn(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('x', "int|\nx", 5);

        self::assertStringEndsWith('on line 2 at column 1', $exception->getMessage());
    }

    #[Test]
    public function theLongStatementIsTruncated(): void
    {
        $statement = \str_repeat('x', 100) . 'foo';
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected('foo', $statement, 100);

        self::assertStringContainsString('…', $exception->getMessage());
        self::assertStringNotContainsString($statement, $exception->getMessage());
    }

    #[Test]
    public function theUnrecognizedTokenIsReportedWithItsLocation(): void
    {
        $exception = UnrecognizedTokenException::becauseTokenIsUnrecognized('%', 'int|%', 4);

        self::assertSame(
            'Syntax error, unrecognized "%" in "int|%" at column 5',
            $exception->getMessage(),
        );
        self::assertSame(ParseException::ERROR_CODE_UNRECOGNIZED_TOKEN, $exception->getCode());
    }

    #[Test]
    public function theUnrecognizedSyntaxIsReportedWithItsLocation(): void
    {
        $exception = UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized('int|', 4);

        self::assertSame(
            'Internal syntax error, in "int|" at column 5',
            $exception->getMessage(),
        );
        self::assertSame(ParseException::ERROR_CODE_UNEXPECTED_SYNTAX_ERROR, $exception->getCode());
    }

    #[Test]
    public function theBlankStatementIsReportedAsEmpty(): void
    {
        $exception = UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized('   ', 0);

        self::assertStringContainsString('<empty statement>', $exception->getMessage());
    }

    #[Test]
    public function theInternalErrorKeepsThePreviousException(): void
    {
        $previous = new \LogicException('oops');
        $exception = InternalParseException::becauseInternalErrorOccurs('int', $previous);

        self::assertSame(
            'An internal error occurred while parsing "int"',
            $exception->getMessage(),
        );
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame(ParseException::ERROR_CODE_INTERNAL_ERROR, $exception->getCode());
    }

    #[Test]
    public function theUnreadableStatementIsReported(): void
    {
        $exception = InternalParseException::becauseTypeStatementIsUnreadable();

        self::assertSame('Could not read type statement', $exception->getMessage());
        self::assertSame(ParseException::ERROR_CODE_INTERNAL_ERROR, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    #[Test]
    public function theUnreadableSourceIsReportedUsingTheOriginalMessage(): void
    {
        $previous = new class ('source is unreadable') extends \RuntimeException implements
            \Phplrt\Contracts\Source\SourceExceptionInterface {};

        $exception = InternalParseException::becauseSourceIsUnreadable($previous);

        self::assertSame('source is unreadable', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame(ParseException::ERROR_CODE_INTERNAL_ERROR, $exception->getCode());
    }

    #[Test]
    public function theSemanticErrorIsRebasedOntoTheWholeSource(): void
    {
        $exception = SemanticParseException::becauseSemanticErrorOccurs(
            e: ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('a', 10),
            source: (new SourceFactory())->create('array{a: int, a: int}'),
        );

        self::assertSame(
            'Duplicate key "a" in "array{a: int, a: int}" at column 11',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function theSemanticErrorCodeIsShiftedByTheBaseValue(): void
    {
        $semantic = ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('a', 10);

        $exception = SemanticParseException::becauseSemanticErrorOccurs(
            e: $semantic,
            source: (new SourceFactory())->create('array{a: int, a: int}'),
        );

        self::assertSame(
            ParseException::ERROR_CODE_SEMANTIC_ERROR_BASE + $semantic->getCode(),
            $exception->getCode(),
        );
    }

    #[Test]
    public function theSemanticErrorMessageIsCapitalized(): void
    {
        $exception = SemanticParseException::becauseSemanticErrorOccurs(
            e: ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('a'),
            source: (new SourceFactory())->create('array{a: int, a: int}'),
        );

        self::assertStringStartsWith('Duplicate', $exception->getMessage());
    }

    #[Test]
    public function everyErrorCodeIsUnique(): void
    {
        $codes = [
            ParseException::ERROR_CODE_UNEXPECTED_TOKEN,
            ParseException::ERROR_CODE_UNRECOGNIZED_TOKEN,
            ParseException::ERROR_CODE_UNEXPECTED_SYNTAX_ERROR,
            ParseException::ERROR_CODE_INTERNAL_ERROR,
            ParseException::ERROR_CODE_SEMANTIC_ERROR_BASE,
        ];

        self::assertSame($codes, \array_values(\array_unique($codes)));
    }
}
