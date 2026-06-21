<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

use TypeLang\Node\Type\Condition\Condition;

final class TernaryExpressionNode extends TypeNode
{
    public function __construct(
        public Condition $condition,
        public TypeNode $then,
        public TypeNode $else,
    ) {}
}
