<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The whitespace after the comma between the items of a single-line list:
 * the fields of a shape, the arguments of a template and the parameters of
 * a callable.
 */
enum CommaSpacing
{
    /**
     * The comma with no whitespace after it: `array<int,string>`.
     */
    case Compact;

    /**
     * The comma with a whitespace after it: `array<int, string>`.
     */
    case Spaced;

    public const DEFAULT = self::Spaced;
}
