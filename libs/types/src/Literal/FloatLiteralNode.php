<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A floating point number.
 *
 * The value is the nearest double, the mantissa and the exponent keep the
 * exact number: `mantissa * 10 ** exponent`. A parser stores the nearest bound
 * of the range (`PHP_FLOAT_MAX`) for a number out of it, never an infinity.
 *
 * ```
 *  -1_5.5e+3  // value: -15500.0, raw: "-1_5.5e+3", mantissa: "-15.5", exponent: "3"
 *  .42        // value: 0.42,     raw: ".42",       mantissa: "0.42",  exponent: "0"
 *  - 1.5      // value: -1.5,     raw: "- 1.5",     mantissa: "-1.5",  exponent: "0"
 * ```
 *
 * A node made by hand has no text to read the exact number from, so the
 * mantissa and the exponent are the ones of the value itself.
 *
 * @template-extends ScalarLiteralNode<float>
 */
final class FloatLiteralNode extends ScalarLiteralNode
{
    /**
     * The number before the exponent, without separators and leading zeros. The
     * integer part is always there, the point is there only with a fraction:
     * `0.5`, `1`, `-0.10`.
     *
     * @var numeric-string
     */
    public readonly string $mantissa;

    /**
     * The power of ten, `0` if there is no exponent.
     *
     * @var numeric-string
     */
    public readonly string $exponent;

    /**
     * @param numeric-string|null $mantissa {@see null} to take it from the value
     * @param numeric-string|null $exponent {@see null} to take it from the value
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of a non-finite value is passed with no raw text, no mantissa or no exponent
     */
    public function __construct(
        float $value,
        ?string $mantissa = null,
        ?string $exponent = null,
        ?string $raw = null,
        int $offset = 0,
    ) {
        if ($mantissa === null || $exponent === null) {
            [$valueMantissa, $valueExponent] = self::splitValue($value);

            $mantissa ??= $valueMantissa;
            $exponent ??= $valueExponent;
        }

        $this->mantissa = $mantissa;
        $this->exponent = $exponent;

        parent::__construct(
            value: $value,
            raw: $raw ?? self::representAsRawValue($value),
            offset: $offset,
        );
    }

    /**
     * @return non-empty-string
     * @throws \InvalidArgumentException in case of a non-finite value
     */
    private static function representAsRawValue(float $value): string
    {
        if (!\is_finite($value)) {
            throw new \InvalidArgumentException('A non-finite float has no literal form, raw text is required');
        }

        /** @var non-empty-string */
        return \var_export($value, true);
    }

    /**
     * Splits the shortest text of the value into the mantissa and the exponent:
     * `1.0E+25` is `1.0` and `25`.
     *
     * @return array{numeric-string, numeric-string}
     * @throws \InvalidArgumentException in case of a non-finite value
     */
    private static function splitValue(float $value): array
    {
        if (!\is_finite($value)) {
            throw new \InvalidArgumentException('A non-finite float requires the mantissa and the exponent');
        }

        $text = \var_export($value, true);
        $position = \strcspn($text, 'E');

        /** @var array{numeric-string, numeric-string} */
        return [
            \substr($text, 0, $position),
            (string) (int) \substr($text, $position + 1),
        ];
    }
}
