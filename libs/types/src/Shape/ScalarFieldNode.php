<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * A field of a shape keyed by a number or by a string.
 *
 * ```
 *  array{0: string, 1: int}
 *  //    ^          ^ the keys
 *
 *  array{"some key": string}
 *  //    ^^^^^^^^^^ the key
 * ```
 *
 * A key is a number or a string and nothing else, the way a key of an array
 * is. A `true` and a `null` are keys of a {@see NamedFieldNode} instead: In
 * the place of a key they are read as the words they are written with, not as
 * the values they name elsewhere.
 *
 * @template-extends ExplicitFieldNode<IntLiteralNode|StringLiteralNode>
 */
final class ScalarFieldNode extends ExplicitFieldNode implements SimpleFieldNodeInterface
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        IntLiteralNode|StringLiteralNode $key,
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

    public function getIndex(): string
    {
        /** @var non-empty-string */
        return (string) $this->key->value;
    }
}
