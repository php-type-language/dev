<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Condition;

use TypeLang\Node\Node;
use TypeLang\Node\Type\TypeNode;

abstract class Condition extends Node
{
    public function __construct(
        public TypeNode $subject,
        public TypeNode $target,
    ) {}
}
