<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A string, kept both decoded and as it is written.
 *
 * ```
 *  "\x41\x42"  // value: "AB", raw: "\"\x41\x42\""
 * ```
 *
 * @template-extends ScalarLiteralNode<string>
 */
final class StringLiteralNode extends ScalarLiteralNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        string $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct($value, $raw ?? self::createRawValue($value), $offset);
    }

    /**
     * Writes a value as a double-quoted string, escaping the control
     * characters, the quote and the backslash.
     */
    private static function createRawValue(string $value): string
    {
        return '"' . \addcslashes($value, "\0..\37\"\\\177") . '"';
    }
}
