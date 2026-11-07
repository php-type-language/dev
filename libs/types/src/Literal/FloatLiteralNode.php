<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A floating point number.
 *
 * The value is the nearest double, the mantissa and the exponent keep the
 * exact number: `mantissa * 10 ** exponent`.
 *
 * ```
 *  -1_5.5e+3  // value: -15500.0, raw: "-1_5.5e+3", mantissa: "-15.5", exponent: "3"
 *  .42        // value: 0.42,     raw: ".42",       mantissa: ".42",   exponent: "0"
 * ```
 *
 * @template-extends ScalarLiteralNode<float>
 */
final class FloatLiteralNode extends ScalarLiteralNode
{
    /**
     * The number before the exponent, without separators.
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
     * @param numeric-string|null $mantissa {@see null} to take it from the raw text
     * @param numeric-string|null $exponent {@see null} to take it from the raw text
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of a non-finite value is passed with no raw text
     */
    public function __construct(
        float $value,
        ?string $mantissa = null,
        ?string $exponent = null,
        ?string $raw = null,
        int $offset = 0,
    ) {
        $raw ??= self::representAsRawValue($value);

        [$rawMantissa, $rawExponent] = self::splitRawValue($raw);

        $this->mantissa = $mantissa ?? $rawMantissa;
        $this->exponent = $exponent ?? $rawExponent;

        parent::__construct(
            value: $value,
            raw: $raw,
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
     * @param non-empty-string $raw
     * @return array{numeric-string, numeric-string}
     */
    private static function splitRawValue(string $raw): array
    {
        $text = \ltrim(\str_replace('_', '', $raw), '+');
        $position = \strcspn($text, 'eE');

        $mantissa = self::trimMantissa(\substr($text, 0, $position));
        $exponent = $position === \strlen($text) ? '0' : self::trimExponent(\substr($text, $position + 1));

        \assert(\is_numeric($mantissa) && \is_numeric($exponent));

        return [$mantissa, $exponent];
    }

    /**
     * Drops the leading zeros, the one before the point stays: `-055` is `-55`, `00.5` is `0.5`.
     */
    private static function trimMantissa(string $mantissa): string
    {
        $sign = $mantissa[0] === '-' ? '-' : '';
        $digits = \ltrim($mantissa, '-');
        $trimmed = \ltrim($digits, '0');

        if ($digits[0] === '0' && ($trimmed === '' || $trimmed[0] === '.')) {
            $trimmed = '0' . $trimmed;
        }

        return $sign . $trimmed;
    }

    /**
     * Drops the plus sign and the leading zeros: `+007` is `7`, `-00` is `0`.
     */
    private static function trimExponent(string $exponent): string
    {
        $digits = \ltrim($exponent, '+-0');

        if ($digits === '') {
            return '0';
        }

        return ($exponent[0] === '-' ? '-' : '') . $digits;
    }
}
