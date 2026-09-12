<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * A single template argument, along with the hint it may carry.
 *
 * ```
 *  Some\Any<int, covariant string>
 *  //       ^^^                    simple argument
 *  //            ^^^^^^^^^         a hinted argument
 * ```
 */
final class TemplateArgumentNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $value,
        /**
         * Word standing in front of the argument, or {@see null} in case of it
         * carries none.
         *
         * What the word means is left to whoever reads the tree. A `covariant`
         * or an `out` is an ordinary identifier as far as the grammar goes.
         *
         * ```
         *  Some\Any<covariant string>
         *  //       ^^^^^^^^^ the hint
         * ```
         */
        public ?Identifier $hint = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
