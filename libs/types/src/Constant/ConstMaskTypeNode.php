<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

/**
 * A global constant referenced by a name written in part.
 *
 * ```
 *  Some\Any\SOME_*
 *  ^^^^^^^^        the namespace the constant belongs to
 *           ^^^^^^ the mask its name is written as
 *
 *  *_SOME
 *  ^^^^^^ a mask with no namespace at all
 * ```
 */
final class ConstMaskTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The name of the constant, written in part.
         */
        public MaskNode $mask,
        /**
         * The namespace the constant belongs to, or {@see null} in case of
         * the constant is written with none. The root namespace is a lone
         * separator.
         *
         * ```
         *  Some\Any\SOME_*  // Some\Any
         *  \SOME_*          // \
         *  SOME_*           // null
         * ```
         */
        public ?Name $namespace = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
