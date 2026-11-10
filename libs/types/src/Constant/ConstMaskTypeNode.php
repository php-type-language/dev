<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\TypeNode;

/**
 * A set of constants, referenced by a name written in part.
 *
 * ```
 *  Some\Any::SOME_*
 *  ^^^^^^^^         the class the constants belong to
 *            ^^^^^^ the mask their names are written as
 *
 *  Some\Any\SOME_*
 *  ^^^^^^^^        the namespace the constants belong to
 *
 *  *_SOME
 *  ^^^^^^ a mask with no class and no namespace at all
 *
 *  \SOME_*
 *  ^       a mask of the root namespace
 * ```
 *
 * A name written whole stands for a single constant, see the
 * {@see ConstTypeNode}.
 */
final class ConstMaskTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The class or the namespace the constants belong to, or
         * {@see null} in case of the mask is written with neither.
         *
         * ```
         *  Some\Any::SOME_*  // the class Some\Any
         *  Some\Any\SOME_*   // the namespace Some\Any
         *  \SOME_*           // the root namespace
         *  SOME_*            // null
         * ```
         */
        public Name|NamespaceName|null $owner,
        /**
         * The name of the constants, written in part.
         */
        public MaskNode $mask,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->mask = clone $this->mask;

        if ($this->owner !== null) {
            $this->owner = clone $this->owner;
        }
    }
}
