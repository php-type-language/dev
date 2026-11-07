<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A `null`, in any case it is written in.
 *
 * ```
 *  NulL  // value: null, raw: "NulL"
 * ```
 *
 * @template-extends LiteralNode<null>
 */
final class NullLiteralNode extends LiteralNode
{
    /**
     * @param non-empty-string|null $raw
     * @param int<0, max> $offset
     */
    public function __construct(?string $raw = null, int $offset = 0)
    {
        parent::__construct(
            value: null,
            raw: $raw ?? self::representAsRawValue(),
            offset: $offset,
        );
    }

    /**
     * @return non-empty-string
     */
    private static function representAsRawValue(): string
    {
        return 'null';
    }
}
