<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Exception\FeatureNotAllowedException;
use TypeLang\Parser\Exception\InternalSemanticException;
use TypeLang\Parser\Exception\InvalidConditionalOperatorException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\ShapeFieldDuplicationException;
use TypeLang\Parser\Exception\ShapeKeysMixingException;
use TypeLang\Parser\Exception\VariadicRedefinitionException;
use TypeLang\Parser\Exception\VariadicWithDefaultException;
use TypeLang\Parser\Tests\TestCase;

final class SemanticExceptionTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{\Closure(int):SemanticException}>
     */
    public static function factoryDataProvider(): iterable
    {
        yield 'feature not allowed' => [
            static fn(int $offset): SemanticException
                => FeatureNotAllowedException::becauseFeatureIsNotAllowed('shapes', $offset),
        ];

        yield 'unexpected sub-node' => [
            static fn(int $offset): SemanticException
                => InternalSemanticException::becauseSubNodeIsUnexpected('Example', $offset),
        ];

        yield 'invalid conditional operator' => [
            static fn(int $offset): SemanticException
                => InvalidConditionalOperatorException::becauseConditionalOperatorIsInvalid('~', $offset),
        ];

        yield 'shape field duplication' => [
            static fn(int $offset): SemanticException
                => ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('key', $offset),
        ];

        yield 'shape keys mixing' => [
            static fn(int $offset): SemanticException
                => ShapeKeysMixingException::becauseShapeKeysAreMixed($offset),
        ];

        yield 'variadic redefinition' => [
            static fn(int $offset): SemanticException
                => VariadicRedefinitionException::becauseVariadicIsRedefined($offset),
        ];

        yield 'variadic with default' => [
            static fn(int $offset): SemanticException
                => VariadicWithDefaultException::becauseVariadicHasDefault($offset),
        ];
    }

    /**
     * @param \Closure(int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionIsAParserException(\Closure $factory): void
    {
        $exception = $factory(0);

        self::assertInstanceOf(ParserExceptionInterface::class, $exception);
        self::assertInstanceOf(\LogicException::class, $exception);
    }

    /**
     * @param \Closure(int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionKeepsTheOffset(\Closure $factory): void
    {
        $exception = $factory(42);

        self::assertSame(42, $exception->offset);
        self::assertSame(42, $exception->getOffset());
    }

    /**
     * @param \Closure(int):SemanticException $factory
     */
    #[DataProvider('factoryDataProvider')]
    #[Test]
    public function everySemanticExceptionHasANonEmptyMessage(\Closure $factory): void
    {
        self::assertNotSame('', $factory(0)->getMessage());
    }

    #[Test]
    public function theFeatureNameIsCapitalizedInTheMessage(): void
    {
        $exception = FeatureNotAllowedException::becauseFeatureIsNotAllowed('shape fields');

        self::assertSame('Shape fields not allowed', $exception->getMessage());
    }

    #[Test]
    public function theFeatureNotAllowedOffsetDefaultsToZero(): void
    {
        self::assertSame(0, FeatureNotAllowedException::becauseFeatureIsNotAllowed('shapes')->offset);
    }

    #[Test]
    public function theUnexpectedSubNodeIsReported(): void
    {
        $exception = InternalSemanticException::becauseSubNodeIsUnexpected('Example');

        self::assertSame(
            'Internal error, unexpected square bracket sub-node Example',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function theInvalidConditionalOperatorIsReported(): void
    {
        $exception = InvalidConditionalOperatorException::becauseConditionalOperatorIsInvalid('~');

        self::assertSame('Invalid conditional operator "~"', $exception->getMessage());
        self::assertSame(SemanticException::ERROR_CODE_INVALID_OPERATOR, $exception->getCode());
    }

    #[Test]
    public function theDuplicatedShapeFieldIsReported(): void
    {
        $exception = ShapeFieldDuplicationException::becauseShapeFieldIsDuplicated('key');

        self::assertSame('Duplicate key "key"', $exception->getMessage());
        self::assertSame(SemanticException::ERROR_CODE_SHAPE_KEY_DUPLICATION, $exception->getCode());
    }

    #[Test]
    public function theMixedShapeKeysAreReported(): void
    {
        $exception = ShapeKeysMixingException::becauseShapeKeysAreMixed();

        self::assertSame('Cannot mix explicit and implicit shape keys', $exception->getMessage());
        self::assertSame(SemanticException::ERROR_CODE_SHAPE_KEY_MIX, $exception->getCode());
    }

    #[Test]
    public function theRedefinedVariadicIsReported(): void
    {
        $exception = VariadicRedefinitionException::becauseVariadicIsRedefined();

        self::assertSame(
            'Either prefix or postfix variadic syntax should be used, but not both',
            $exception->getMessage(),
        );
        self::assertSame(SemanticException::ERROR_CODE_VARIADIC_ALREADY_VARIADIC, $exception->getCode());
    }

    #[Test]
    public function theVariadicWithADefaultIsReported(): void
    {
        $exception = VariadicWithDefaultException::becauseVariadicHasDefault();

        self::assertSame('Cannot have variadic param with a default', $exception->getMessage());
        self::assertSame(SemanticException::ERROR_CODE_VARIADIC_WITH_DEFAULT, $exception->getCode());
    }

    #[Test]
    public function theInternalErrorsHaveNoDedicatedCode(): void
    {
        self::assertSame(0, InternalSemanticException::becauseSubNodeIsUnexpected('Example')->getCode());
        self::assertSame(0, FeatureNotAllowedException::becauseFeatureIsNotAllowed('shapes')->getCode());
    }

    #[Test]
    public function everyErrorCodeIsUnique(): void
    {
        $codes = [
            SemanticException::ERROR_CODE_SHAPE_KEY_DUPLICATION,
            SemanticException::ERROR_CODE_SHAPE_KEY_MIX,
            SemanticException::ERROR_CODE_VARIADIC_WITH_DEFAULT,
            SemanticException::ERROR_CODE_VARIADIC_ALREADY_VARIADIC,
            SemanticException::ERROR_CODE_INVALID_OPERATOR,
        ];

        self::assertSame($codes, \array_values(\array_unique($codes)));
    }
}
