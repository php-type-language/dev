<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<float>
 *
 * @phpstan-consistent-constructor
 */
final class FloatLiteralNode extends LiteralNode
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
