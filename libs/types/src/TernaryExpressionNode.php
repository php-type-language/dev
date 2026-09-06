<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Condition\Condition;

final class TernaryExpressionNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Condition $condition,
        public TypeNode $then,
        public TypeNode $else,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
