<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The whitespaces around the ampersand of an intersection.
 */
enum IntersectionSpacing
{
    /**
     * The types joined with no whitespaces: `A&B`.
     */
    case Compact;

    /**
     * The types joined with whitespaces around the ampersand: `A & B`.
     */
    case Spaced;

    public const DEFAULT = self::Spaced;
}
