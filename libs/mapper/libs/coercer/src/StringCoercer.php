<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Coercer;

/**
 * Converts integers and finite floats to their decimal notation.
 *
 * A float is written in the shortest form that is read back to the same
 * number, as {@see \json_encode()} does by default.
 *
 * @template-implements CoercerInterface<string>
 */
final class StringCoercer implements CoercerInterface
{
    public function coerce(mixed $value): ?string
    {
        if (\is_string($value)) {
            return $value;
        }

        if (\is_int($value)) {
            return (string) $value;
        }

        if (\is_float($value)) {
            return self::coerceFloat($value);
        }

        return null;
    }

    private static function coerceFloat(float $value): ?string
    {
        if (!\is_finite($value)) {
            return null;
        }

        $result = \json_encode($value);

        if ($result !== false && (float) $result === $value) {
            return $result;
        }

        return \sprintf('%.17G', $value);
    }
}
