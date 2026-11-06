<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A floating point number.
 *
 * ```
 *  -1.5e+3  // value: -1500.0, raw: "-1.5e+3"
 *  .42      // value: 0.42,    raw: ".42"
 * ```
 *
 * @template-extends ScalarLiteralNode<float>
 */
final class FloatLiteralNode extends ScalarLiteralNode
{
    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of a non-finite value is passed with no raw text
     */
    public function __construct(
        float $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct($value, $raw ?? self::createRawValue($value), $offset);
    }

    /**
     * Writes a value as a float literal, never as an integer one.
     */
    private static function createRawValue(float $value): string
    {
        if (!\is_finite($value)) {
            throw new \InvalidArgumentException('A non-finite float has no literal form, raw text is required');
        }

        return \var_export($value, true);
    }
}
