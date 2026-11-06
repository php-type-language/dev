<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * A field of a shape keyed by something that has to be read to be understood:
 * A reference to a constant, the mask of a family of them, or any other type.
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
 *
 *  WeakMap{User<Some>: T}
 *  //      ^^^^^^^^^^ a type
 * ```
 *
 * @template-extends ExplicitFieldNode<TypeNode>
 */
final class ComplexFieldNode extends ExplicitFieldNode
{
    /**
     * A key that is an int, a string, a bool or a null belongs to a
     * {@see ScalarFieldNode} or a {@see NamedFieldNode}, so it is not
     * accepted here. The check is made on creation only.
     *
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the key is a literal other node types stand for
     */
    public function __construct(
        TypeNode $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        if (
            $key instanceof IntLiteralNode
            || $key instanceof StringLiteralNode
            || $key instanceof BoolLiteralNode
            || $key instanceof NullLiteralNode
        ) {
            throw new \InvalidArgumentException(\sprintf(
                'A %s key must be a complex type, use a %s or a %s for a literal',
                self::class,
                ScalarFieldNode::class,
                NamedFieldNode::class,
            ));
        }

        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }
}
