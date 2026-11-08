<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
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
         *  \SOME_*          // \ (the root namespace)
         *  SOME_*           // null
         * ```
         */
        public ?NamespaceName $namespace = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * Whether the reference is read from the root namespace, that is, written
     * with the leading separator.
     *
     * ```
     *  \SOME_*           // true
     *  \Some\Any\SOME_*  // true
     *  Some\Any\SOME_*   // false
     *  SOME_*            // false
     * ```
     *
     * @api
     */
    public function isFullyQualified(): bool
    {
        return $this->namespace?->kind === NamespaceKind::FullyQualified;
    }
}
