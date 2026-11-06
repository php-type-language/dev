<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A whole number, written in any of the four radixes.
 *
 * ```
 *  0xFE_DE  // value: 65246, raw: "0xFE_DE", decimal: "65246"
 *  042      // value: 34,    raw: "042",     decimal: "34"
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
     * @param int<0, max> $offset
     */
    public function __construct(
        int $value,
        ?string $raw = null,
        ?string $decimal = null,
        int $offset = 0,
    ) {
        $this->decimal = $decimal ?? (string) $value;

        parent::__construct($value, $raw ?? (string) $value, $offset);
    }
}
