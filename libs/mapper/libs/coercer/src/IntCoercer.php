<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Coercer;

/**
 * Converts integer strings and integral floats to `int`.
 *
 * @template-implements CoercerInterface<int>
 */
final class IntCoercer implements CoercerInterface
{
    private const FLOAT_LOWER_BOUND = -(\PHP_INT_MAX + 1);

    private const FLOAT_UPPER_BOUND = \PHP_INT_MAX + 1;

    public function coerce(mixed $value): ?int
    {
        if (\is_int($value)) {
            return $value;
        }

        if (\is_string($value)) {
            return self::coerceString($value);
        }

        if (\is_float($value)) {
            return self::coerceFloat($value);
        }

        return null;
    }

    private static function coerceString(string $value): ?int
    {
        if (!\is_numeric($value) || self::isPadded($value)) {
            return null;
        }

        $number = $value + 0;

        if (\is_int($number)) {
            return $number;
        }

        return null;
    }

    private static function coerceFloat(float $value): ?int
    {
        if ($value >= self::FLOAT_LOWER_BOUND && $value < self::FLOAT_UPPER_BOUND) {
            $number = (int) $value;

            if ((float) $number === $value) {
                return $number;
            }
        }

        return null;
    }

    /**
     * Returns {@see true} if the numeric string has leading or trailing
     * whitespace.
     *
     * @param numeric-string $value
     */
    private static function isPadded(string $value): bool
    {
        return \ord($value[0]) <= 0x20 || \ord($value[-1]) <= 0x20;
    }
}
