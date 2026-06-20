<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Condition;

use TypeLang\Node\Node;
use TypeLang\Node\Stmt\TypeStatement;

abstract class Condition extends Node
{
    public function __construct(
        public TypeStatement $subject,
        public TypeStatement $target,
    ) {}
}
