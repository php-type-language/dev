<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Condition;

use TypeLang\Type\Statement;
use TypeLang\Type\Stmt\TypeStatement;

abstract class Condition extends Statement
{
    public function __construct(
        public TypeStatement $subject,
        public TypeStatement $target,
    ) {}
}
