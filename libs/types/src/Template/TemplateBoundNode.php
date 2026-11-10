<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * One end a template parameter is bounded at, written as the word it is
 * bounded with and the type the bound is expressed by.
 *
 * ```
 *  callable<T of Some super Any>(T): T
 *  //         ^^^^^^^                  the upper bound
 *  //                 ^^^^^^^^^        the lower bound
 * ```
 */
final class TemplateBoundNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $type,
        /**
         * The word the bound is written with: `of` and `as` bound from
         * above, `super` from below.
         */
        public Identifier $operator,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->type = clone $this->type;
        $this->operator = clone $this->operator;
    }
}
