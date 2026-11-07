<?php

declare(strict_types=1);

namespace TypeLang\Printer\PrinterOptions;

/**
 * The whitespace between the colon and the return type of a callable.
 */
enum CallableReturnSpacing
{
    /**
     * The type follows the colon with no whitespace: `callable():void`.
     */
    case Compact;

    /**
     * The type follows the colon and a whitespace: `callable(): void`.
     */
    case Spaced;

    public const DEFAULT = self::Spaced;
}
