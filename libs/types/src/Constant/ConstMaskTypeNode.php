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
 *
 *  \SOME_*
 *  ^       a mask of the root namespace
 * ```
 */
final class ConstMaskTypeNode extends TypeNode
{
    /**
     * Whether the reference is read from the root namespace, that is, written
     * with the leading separator.
     *
     * A namespace tells it by itself, so the flag is the one of the namespace
     * in case of the constant is written with one.
     *
     * ```
     *  \SOME_*           // true
     *  \Some\Any\SOME_*  // true
     *  Some\Any\SOME_*   // false
     *  SOME_*            // false
     * ```
     */
    public readonly bool $isFullyQualified;

    /**
     * @param bool $isFullyQualified the flag of a mask written with no namespace
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The name of the constant, written in part.
         */
        public MaskNode $mask,
        /**
         * The namespace the constant belongs to, or {@see null} in case of
         * the constant is written with none.
         *
         * ```
         *  Some\Any\SOME_*  // Some\Any
         *  \Some\Any\SOME_* // \Some\Any
         *  SOME_*           // null
         *  \SOME_*          // null
         * ```
         */
        public ?Name $namespace = null,
        bool $isFullyQualified = false,
        int $offset = 0,
    ) {
        $this->isFullyQualified = $namespace->isFullyQualified ?? $isFullyQualified;

        parent::__construct($offset);
    }
}
