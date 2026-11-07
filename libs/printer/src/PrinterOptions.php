<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use TypeLang\Printer\PrinterOptions\CallableReturnSpacing;
use TypeLang\Printer\PrinterOptions\UnionSpacing;

/**
 * The formatting settings of the printer.
 */
final class PrinterOptions
{
    /**
     * @var non-empty-string
     */
    public const DEFAULT_NEW_LINE_DELIMITER = "\n";

    /**
     * @var non-empty-string
     */
    public const DEFAULT_INDENTATION = '    ';

    public const DEFAULT_UNION_SPACING = UnionSpacing::DEFAULT;

    public const DEFAULT_INTERSECTION_SPACING = IntersectionSpacing::DEFAULT;

    public const DEFAULT_CALLABLE_RETURN_SPACING = CallableReturnSpacing::DEFAULT;

    /**
     * @var int<0, max>
     */
    public const DEFAULT_MAX_INLINE_SHAPE_FIELDS = 1;

    /**
     * @var int<0, max>
     */
    public const DEFAULT_MAX_INLINE_TEMPLATE_ITEMS = \PHP_INT_MAX;

    /**
     * @var int<0, max>
     */
    public const DEFAULT_MAX_INLINE_CALLABLE_PARAMETERS = \PHP_INT_MAX;

    public const DEFAULT_COMMA_SPACING = CommaSpacing::DEFAULT;

    public const DEFAULT_NULLABLE_STYLE = NullableStyle::DEFAULT;

    public const DEFAULT_LIST_STYLE = ListStyle::DEFAULT;

    /**
     * @throws \InvalidArgumentException in case of a negative maximum of the items in one line
     */
    public function __construct(
        /**
         * The line delimiter of multiline lists.
         *
         * @var non-empty-string
         */
        public readonly string $newLine = self::DEFAULT_NEW_LINE_DELIMITER,
        /**
         * A single level of the indentation of multiline lists.
         *
         * @var non-empty-string
         */
        public readonly string $indentation = self::DEFAULT_INDENTATION,
        /**
         * The whitespaces around the pipe of a union.
         *
         * ```
         * $unionSpacing = UnionSpacing::Compact;
         * // Type|Some|Any
         *
         * $unionSpacing = UnionSpacing::Spaced;
         * // Type | Some | Any
         * ```
         */
        public readonly UnionSpacing $unionSpacing = self::DEFAULT_UNION_SPACING,
        /**
         * The whitespaces around the ampersand of an intersection.
         *
         * ```
         * $intersectionSpacing = IntersectionSpacing::Compact;
         * // Type&Some&Any
         *
         * $intersectionSpacing = IntersectionSpacing::Spaced;
         * // Type & Some & Any
         * ```
         */
        public readonly IntersectionSpacing $intersectionSpacing = self::DEFAULT_INTERSECTION_SPACING,
        /**
         * The whitespace between the colon and the return type of callable.
         *
         * ```
         * $callableReturnSpacing = CallableReturnSpacing::Compact;
         * // callable():void
         *
         * $callableReturnSpacing = CallableReturnSpacing::Spaced;
         * // callable(): void
         * ```
         */
        public readonly CallableReturnSpacing $callableReturnSpacing = self::DEFAULT_CALLABLE_RETURN_SPACING,
        /**
         * The maximum number of fields of a shape that are written in one
         * line. A shape of more fields is written one field per line.
         *
         * ```
         * $maxInlineShapeFields = 2;
         * // array{some, any}
         *
         * $maxInlineShapeFields = 1;
         * // array{
         * //     some,
         * //     any
         * // }
         * ```
         *
         * @var int<0, max>
         */
        public readonly int $maxInlineShapeFields = self::DEFAULT_MAX_INLINE_SHAPE_FIELDS,
        /**
         * The maximum number of template arguments (or parameters) that are
         * written in one line. More of them are written one per line.
         *
         * ```
         * $maxInlineTemplateItems = 2;
         * // array<K, V>
         *
         * $maxInlineTemplateItems = 1;
         * // array<
         * //     K,
         * //     V
         * // >
         * ```
         *
         * @var int<0, max>
         */
        public readonly int $maxInlineTemplateItems = self::DEFAULT_MAX_INLINE_TEMPLATE_ITEMS,
        /**
         * The maximum number of callable parameters that are written in one
         * line. More of them are written one per line.
         *
         * ```
         * $maxInlineCallableParameters = 2;
         * // callable(int, string): void
         *
         * $maxInlineCallableParameters = 1;
         * // callable(
         * //     int,
         * //     string
         * // ): void
         * ```
         *
         * @var int<0, max>
         */
        public readonly int $maxInlineCallableParameters = self::DEFAULT_MAX_INLINE_CALLABLE_PARAMETERS,
        /**
         * The whitespace after the comma between the items of a single-line
         * list: the fields of a shape, the arguments of a template and the
         * parameters of a callable.
         *
         * ```
         * $commaSpacing = CommaSpacing::Compact;
         * // array<int,string>
         *
         * $commaSpacing = CommaSpacing::Spaced;
         * // array<int, string>
         * ```
         */
        public readonly CommaSpacing $commaSpacing = self::DEFAULT_COMMA_SPACING,
        /**
         * The way a nullable type is written.
         *
         * ```
         * $nullableStyle = NullableStyle::Question;
         * // ?int
         *
         * $nullableStyle = NullableStyle::Union;
         * // int|null
         * ```
         */
        public readonly NullableStyle $nullableStyle = self::DEFAULT_NULLABLE_STYLE,
        /**
         * The way a list of a type is written.
         *
         * ```
         * $listStyle = ListStyle::Brackets;
         * // int[]
         *
         * $listStyle = ListStyle::Generic;
         * // array<int>
         * ```
         */
        public readonly ListStyle $listStyle = self::DEFAULT_LIST_STYLE,
    ) {
        if ($maxInlineShapeFields < 0) {
            throw new \InvalidArgumentException('The "$maxInlineShapeFields" option must not be negative');
        }

        if ($maxInlineTemplateItems < 0) {
            throw new \InvalidArgumentException('The "$maxInlineTemplateItems" option must not be negative');
        }

        if ($maxInlineCallableParameters < 0) {
            throw new \InvalidArgumentException('The "$maxInlineCallableParameters" option must not be negative');
        }
    }
}
