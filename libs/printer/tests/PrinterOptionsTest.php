<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\ListStyle;

#[CoversClass(PrinterOptions::class)]
final class PrinterOptionsTest extends TestCase
{
    public function testDefaultsMatchTheConstants(): void
    {
        $options = new PrinterOptions();

        self::assertSame(PrinterOptions::DEFAULT_NEW_LINE_DELIMITER, $options->newLine);
        self::assertSame(PrinterOptions::DEFAULT_INDENTATION, $options->indentation);
        self::assertSame(PrinterOptions::DEFAULT_UNION_SPACING, $options->unionSpacing);
        self::assertSame(PrinterOptions::DEFAULT_INTERSECTION_SPACING, $options->intersectionSpacing);
        self::assertSame(PrinterOptions::DEFAULT_CALLABLE_RETURN_SPACING, $options->callableReturnSpacing);
        self::assertSame(PrinterOptions::DEFAULT_MAX_INLINE_SHAPE_FIELDS, $options->maxInlineShapeFields);
        self::assertSame(PrinterOptions::DEFAULT_MAX_INLINE_TEMPLATE_ITEMS, $options->maxInlineTemplateItems);
        self::assertSame(PrinterOptions::DEFAULT_MAX_INLINE_CALLABLE_PARAMETERS, $options->maxInlineCallableParameters);
        self::assertSame(PrinterOptions::DEFAULT_COMMA_SPACING, $options->commaSpacing);
        self::assertSame(PrinterOptions::DEFAULT_NULLABLE_STYLE, $options->nullableStyle);
        self::assertSame(PrinterOptions::DEFAULT_LIST_STYLE, $options->listStyle);
    }

    public function testDefaultsNeverBreakTemplatesAndCallablesIntoLines(): void
    {
        self::assertSame(\PHP_INT_MAX, PrinterOptions::DEFAULT_MAX_INLINE_TEMPLATE_ITEMS);
        self::assertSame(\PHP_INT_MAX, PrinterOptions::DEFAULT_MAX_INLINE_CALLABLE_PARAMETERS);
    }

    public function testValuesAreStored(): void
    {
        $options = new PrinterOptions(
            newLine: "\r\n",
            indentation: "\t",
            maxInlineShapeFields: 3,
            listStyle: ListStyle::Generic,
        );

        self::assertSame("\r\n", $options->newLine);
        self::assertSame("\t", $options->indentation);
        self::assertSame(3, $options->maxInlineShapeFields);
        self::assertSame(ListStyle::Generic, $options->listStyle);
    }

    public function testZeroThresholdIsAllowed(): void
    {
        $options = new PrinterOptions(
            maxInlineShapeFields: 0,
            maxInlineTemplateItems: 0,
            maxInlineCallableParameters: 0,
        );

        self::assertSame(0, $options->maxInlineShapeFields);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function thresholdsDataProvider(): iterable
    {
        yield 'shape' => ['maxInlineShapeFields'];
        yield 'template' => ['maxInlineTemplateItems'];
        yield 'callable' => ['maxInlineCallableParameters'];
    }

    /**
     * @param non-empty-string $option
     */
    #[DataProvider('thresholdsDataProvider')]
    public function testNegativeThresholdIsRejected(string $option): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($option);

        new PrinterOptions(...[$option => -1]);
    }
}
