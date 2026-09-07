<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A global constant referenced by a name written in part.
 *
 * ```
 *  Some\Any\SOME_*
 *  ^^^^^^^^        the namespace the constant belongs to
 *           ^^^^^^ the mask its name is written as
 *
 *  *_SOME
 *  ^^^^^^ a mask alone: The name may be left unsaid from its very beginning
 * ```
 */
final class ConstMaskNode extends TypeNode
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
         * The namespace the constant belongs to, or {@see null} in case of it
         * is written with none. Always a relative name: The leading separator
         * a fully qualified reference begins in is the
         * {@see $isFullyQualified} below.
         *
         * ```
         *  SOME_*             // null
         *  Some\Any\SOME_*    // "Some\Any"
         * ```
         */
        public ?Name $namespace = null,
        /**
         * Whether the reference is written with the leading separator that
         * says it is to be read from the root namespace.
         *
         * ```
         *  Some\SOME_*   // false
         *  \Some\SOME_*  // true
         *  \SOME_*       // true, with no namespace at all
         * ```
         */
        public bool $isFullyQualified = Name::IS_FULLY_QUALIFIED_DEFAULT_VALUE,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
