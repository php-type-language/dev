<?php

declare(strict_types=1);

namespace TypeLang\Parser\Literal;

use TypeLang\Type\Literal\IntLiteralNode;

final class IntLiteralParser
{
    /**
     * Parse raw integer literal string value: Decimal, hexadecimal
     * (like a "0xFF"), octal (like a "0o17" or a "017") and binary
     * (like a "0b1010") ones.
     */
    public static function parse(string $value): IntLiteralNode
    {
        [$negative, $decimal] = self::split($value);

        $inverse = '-' . $decimal;

        if ($negative) {
            if ((string) \PHP_INT_MIN === $inverse) {
                return new IntLiteralNode(\PHP_INT_MIN, $value, $inverse);
            }

            /** @phpstan-ignore-next-line : An "$inverse" variable contain numeric-string */
            return new IntLiteralNode((int) $inverse, $value, $inverse);
        }

        return new IntLiteralNode((int) $decimal, $value, $decimal);
    }

    /**
     * @return array{bool, numeric-string}
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($negative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            /** @var array{bool, numeric-string} */
            return [$negative, match ($literal[1]) {
                // hexadecimal
                'x', 'X' => \base_convert(\substr($literal, 2), 16, 10),
                // binary
                'b', 'B' => \base_convert(\substr($literal, 2), 2, 10),
                // octal
                'o', 'O' => \base_convert(\substr($literal, 2), 8, 10),
                // octal (legacy)
                default => \base_convert($literal, 8, 10),
            }];
        }

        /** @var array{bool, numeric-string} */
        return [$negative, $literal];
    }
}
