<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

use TypeLang\Type\Stmt\Condition\Condition;

final class TernaryExpressionNode extends TypeStatement
{
    public function __construct(
        public Condition $condition,
        public TypeStatement $then,
        public TypeStatement $else,
    ) {}
}
