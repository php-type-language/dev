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
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     */
    public function __construct(
        bool $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        parent::__construct(
            value: $value,
            raw: $raw ?? self::representAsRawValue($value),
            offset: $offset,
        );
    }

    /**
     * @return non-empty-string
     */
    private static function representAsRawValue(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
