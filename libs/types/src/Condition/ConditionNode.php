<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition;

use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

/**
 * The question a ternary type asks: the two sides of it and the operator
 * between them.
 *
 * ```
 *  ($value is int ? string : bool)
 *  //^^^^^ ^^ ^^^
 *  //subject  target
 *  //      the operator
 * ```
 */
final class ConditionNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode|VariableNode $subject,
        public TypeNode|VariableNode $target,
        /**
         * The kind of the operator between the sides.
         */
        public ConditionKind $kind,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->subject = clone $this->subject;
        $this->target = clone $this->target;
    }
}
