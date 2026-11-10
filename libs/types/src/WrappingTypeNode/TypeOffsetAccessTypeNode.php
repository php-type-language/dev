<?php

declare(strict_types=1);

namespace TypeLang\Type\WrappingTypeNode;

use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode;

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
final class TypeOffsetAccessTypeNode extends WrappingTypeNode
{
    /**
     * @param T $type
     * @param int<0, max> $offset
     */
    public function __construct(
        TypeNode $type,
        /**
         * The type standing in the square brackets.
         */
        public TypeNode $access,
        int $offset = 0,
    ) {
        parent::__construct($type, $offset);
    }

    public function __clone()
    {
        parent::__clone();
        $this->access = clone $this->access;
    }
}
