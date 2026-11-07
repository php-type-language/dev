<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The way a nullable type is written.
 */
enum NullableStyle
{
    /**
     * A nullable type written with the leading question mark: `?int`.
     */
    case Question;

    /**
     * A nullable type written as the union with a `null`: `int|null`.
     */
    case Union;

    public const DEFAULT = self::Question;
}
