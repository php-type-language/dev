<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\TypeNode;

/**
 * A field of a shape keyed by something that has to be read to be understood:
 * A reference to a constant, or the mask of a family of them.
 *
 * ```
 *  array{Some\Any::CONST_NAME: string}
 *  //    ^^^^^^^^^^^^^^^^^^^^          a class constant
 *
 *  array{Some\Any::CONST_*: string}
 *  //    ^^^^^^^^^^^^^^^^^          the mask of a class constant
 *
 *  array{JSON_*: string}
 *  //    ^^^^^^          the mask of a global constant
 * ```
 *
 * Unlike a {@see SimpleFieldNodeInterface}, such a key comes down to no
 * string of its own.
 *
 * Whether two references name the same constant depends on what the names
 * are resolved against, and a written statement does not say that.
 *
 * @template-extends ExplicitFieldNode<TypeNode>
 */
final class ComplexFieldNode extends ExplicitFieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        TypeNode $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }
}
