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
     */
    public function __construct(string $value)
    {
        parent::__construct($value, '$' . $value);
    }
}
