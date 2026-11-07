<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A whole number, written in any of the four radixes.
 *
 * ```
 *  0xFE_DE  // value: 65246, raw: "0xFE_DE", decimal: "65246", kind: Hexadecimal
 *  042      // value: 34,    raw: "042",     decimal: "34",    kind: Octal
 * ```
 *
 * @template-extends ScalarLiteralNode<int>
 */
final class IntLiteralNode extends ScalarLiteralNode
{
    /**
     * The value written out in base 10.
     *
     * @var numeric-string
     */
    public readonly string $decimal;

    /**
     * @param numeric-string|null $decimal
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of no raw text for a non-decimal number out of the native range
     */
    public function __construct(
        int $value,
        ?string $decimal = null,
        public readonly IntLiteralKind $kind = IntLiteralKind::DEFAULT,
        ?string $raw = null,
        int $offset = 0,
    ) {
        $this->decimal = $decimal ?? (string) $value;

        parent::__construct(
            value: $value,
            raw: $raw ?? self::representAsRawValue($value, $this->decimal, $kind),
            offset: $offset,
        );
    }

    /**
     * @param numeric-string $decimal
     * @return non-empty-string
     * @throws \InvalidArgumentException in case of a non-decimal number out of the native range
     */
    private static function representAsRawValue(int $value, string $decimal, IntLiteralKind $kind): string
    {
        if ($kind === IntLiteralKind::Decimal) {
            return $decimal;
        }

        if ($decimal !== (string) $value) {
            throw new \InvalidArgumentException('A number out of the native range requires raw text');
        }

        // The conversions read a negative integer as unsigned: for the lower bound it is the magnitude
        $magnitude = $value === \PHP_INT_MIN ? $value : \abs($value);

        $literal = match ($kind) {
            IntLiteralKind::Hexadecimal => '0x' . \dechex($magnitude),
            IntLiteralKind::Binary => '0b' . \decbin($magnitude),
            IntLiteralKind::Octal => '0o' . \decoct($magnitude),
        };

        return $value < 0 ? '-' . $literal : $literal;
    }
}
