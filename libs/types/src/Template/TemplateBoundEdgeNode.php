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
final class TemplateBoundEdgeNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Identifier $operator,
        public TypeNode $type,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
