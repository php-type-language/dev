<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The whitespaces around the pipe of a union.
 */
enum UnionSpacing
{
    /**
     * The types joined with no whitespaces: `int|string`.
     */
    case Compact;

    /**
     * The types joined with whitespaces around the pipe: `int | string`.
     */
    case Spaced;

    public const DEFAULT = self::Compact;
}
