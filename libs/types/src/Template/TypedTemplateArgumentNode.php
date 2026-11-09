<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\TypeNode;

/**
 * A template argument that is a type, along with the call-site variance it
 * may carry.
 *
 * ```
 *  Some\Any<int, covariant string>
 *  //       ^^^                    an invariant argument
 *  //            ^^^^^^^^^^^^^^^^  a covariant argument
 * ```
 */
final class TypedTemplateArgumentNode extends TemplateArgumentNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $value,
        /**
         * Word standing in front of the argument, or {@see null} in case of it
         * carries none, which makes the argument invariant.
         *
         * What the word means is left to whoever reads the tree. A `covariant`
         * or an `out` is an ordinary identifier as far as the grammar goes.
         *
         * ```
         *  Some\Any<covariant string>
         *  //       ^^^^^^^^^ the variance
         * ```
         */
        public ?Identifier $variance = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
