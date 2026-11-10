<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

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
         * Variance standing in front of the argument, or {@see null} in case of
         * it carries none, which makes the argument invariant.
         *
         * ```
         *  Some\Any<covariant string>
         *  //       ^^^^^^^^^ the variance
         * ```
         */
        public ?VarianceNode $variance = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->value = clone $this->value;

        if ($this->variance !== null) {
            $this->variance = clone $this->variance;
        }
    }
}
