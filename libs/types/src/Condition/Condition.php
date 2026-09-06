<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

abstract class Condition extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $subject,
        public TypeNode $target,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
