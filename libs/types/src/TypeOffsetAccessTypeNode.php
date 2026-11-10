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
 */
final class TypeOffsetAccessTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The type an offset is taken of.
         */
        public TypeNode $type,
        /**
         * The type standing in the square brackets.
         */
        public TypeNode $access,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->type = clone $this->type;
        $this->access = clone $this->access;
    }
}
