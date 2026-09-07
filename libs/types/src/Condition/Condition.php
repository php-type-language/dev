<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

/**
 * The question a ternary type asks, that is, the two sides of it and,
 * in the class itself, the operator between them.
 *
 * ```
 *  ($value is int ? string : bool)
 *  //^^^^^^ ^^ ^^^
 *  //subject    target
 * ```
 */
abstract class Condition extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode|VariableNode $subject,
        public TypeNode|VariableNode $target,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
