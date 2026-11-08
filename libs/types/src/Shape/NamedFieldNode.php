<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

/**
 * A field of a shape keyed by a bare word.
 *
 * ```
 *  array{name: string}
 *  //    ^^^^ the key
 * ```
 *
 * A `true`, a `false` and a `null` are bare words as well. In the place of
 * a key they name nothing but themselves.
 *
 * @template-extends ExplicitFieldNode<Name>
 */
final class NamedFieldNode extends ExplicitFieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        Name $key,
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

    /**
     * Creates a field out of a key written as a bare word.
     *
     * @param non-empty-string $key
     * @param int<0, max> $offset
     */
    public static function from(
        string $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ): self {
        return new self(new Name($key, $offset), $type, $isOptional, $offset);
    }
}
