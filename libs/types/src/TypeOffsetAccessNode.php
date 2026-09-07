<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * The type standing at an offset of another type.
 *
 * ```
 *  Some\Any[int]
 *  ^^^^^^^^      the type an offset is taken of
 *           ^^^  the offset itself
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<T>
 */
final class TypeOffsetAccessNode extends WrappingTypeNode
{
    /**
     * @param T $type
     * @param int<0, max> $offset
     */
    public function __construct(
        TypeNode $type,
        /**
         * The property is named an `$access` rather than an `$offset` so that
         * it is not taken for the place in the source text every node
         * carries.
         */
        public readonly TypeNode $access,
        int $offset = 0,
    ) {
        parent::__construct($type, $offset);
    }
}
