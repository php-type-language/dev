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
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     */
    public function __construct(
        string $value,
        public readonly StringLiteralKind $kind = StringLiteralKind::DEFAULT,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct(
            value: $value,
            raw: $raw ?? self::representAsRawValue($value, $kind),
            offset: $offset,
        );
    }

    /**
     * A single-quoted string escapes only the quote and the backslash.
     *
     * @return non-empty-string
     */
    private static function representAsRawValue(string $value, StringLiteralKind $kind): string
    {
        if ($kind === StringLiteralKind::DoubleQuoted) {
            return \sprintf('"%s"', self::escapeDoubleQuoted($value));
        }

        return \sprintf("'%s'", \addcslashes($value, "'\\"));
    }

    /**
     * Escapes the quote, the backslash and the control characters. The bell
     * and the backspace are written as `\007` and `\010`.
     */
    private static function escapeDoubleQuoted(string $value): string
    {
        return \str_replace(
            ["\x07", "\x08"],
            ['\007', '\010'],
            \addcslashes($value, "\0..\6\11..\37\"\\\177"),
        );
    }
}
