<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<bool>
 *
 * @phpstan-consistent-constructor
 */
final class BoolLiteralNode extends LiteralNode
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
