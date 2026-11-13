<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Coercer;

/**
 * Converts numeric strings and exactly representable integers to `float`.
 *
 * @template-implements CoercerInterface<float>
 */
final class FloatCoercer implements CoercerInterface
{
    private const FLOAT_UPPER_BOUND = \PHP_INT_MAX + 1;

    public function coerce(mixed $value): ?float
    {
        if (\is_float($value)) {
            return $value;
        }

        if (\is_string($value)) {
            return self::coerceString($value);
        }

        if (\is_int($value)) {
            return self::coerceInt($value);
        }

        return null;
    }

    private static function coerceString(string $value): ?float
    {
        if (!\is_numeric($value) || self::isPadded($value)) {
            return null;
        }

        $number = (float) $value;

        if (!\is_finite($number)) {
            return null;
        }

        if ($number === 0.0 && self::hasNonZeroMantissa($value)) {
            return null;
        }

        return $number;
    }

    /**
     * @param numeric-string $value
     */
    private static function hasNonZeroMantissa(string $value): bool
    {
        return \strcspn($value, '123456789') < \strcspn($value, 'eE');
    }

    private static function coerceInt(int $value): ?float
    {
        $number = (float) $value;

        if ($number < self::FLOAT_UPPER_BOUND && (int) $number === $value) {
            return $number;
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
