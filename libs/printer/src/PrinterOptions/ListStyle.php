<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The way a list of a type is written.
 */
enum ListStyle
{
    /**
     * A list of the type written with the square brackets: `int[]`.
     */
    case Brackets;

    /**
     * A list of the type written as the generic array: `array<int>`.
     */
    case Generic;

    public const DEFAULT = self::Brackets;
}
