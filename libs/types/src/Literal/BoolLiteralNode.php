<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A `true` or a `false`, in any case they are written in.
 *
 * ```
 *  TruE  // value: true, raw: "TruE"
 * ```
 *
 * @template-extends ScalarLiteralNode<bool>
 */
final class BoolLiteralNode extends ScalarLiteralNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        bool $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct($value, $raw ?? ($value ? 'true' : 'false'), $offset);
    }
}
