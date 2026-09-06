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
    public function __construct(
        float $value,
        ?string $raw = null,
    ) {
        parent::__construct($value, $raw ?? (string) $value);
    }
}
