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
 * @template-extends ScalarNode<float>
 *
 * @phpstan-consistent-constructor
 */
final class FloatLiteralNode extends ScalarNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        float $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct($value, $raw ?? (string) $value, $offset);
    }
}
