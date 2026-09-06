<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<non-empty-string>
 *
 * @phpstan-consistent-constructor
 */
final class VariableLiteralNode extends LiteralNode
{
    /**
     * @param non-empty-string $value
     * @param int<0, max> $offset
     */
    public function __construct(string $value, int $offset = 0)
    {
        parent::__construct($value, '$' . $value, $offset);
    }
}
