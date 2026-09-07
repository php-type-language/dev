<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Identifier;
use TypeLang\Type\TypeNode;

/**
 * A field of a shape keyed by a bare word.
 *
 * ```
 *  array{name: string}
 *  //    ^^^^ the key
 * ```
 *
 * A `true`, a `false` and a `null` are bare words as well: In the place of
 * a key they name nothing but themselves.
 *
 * @template-extends ExplicitFieldNode<Identifier>
 */
final class NamedFieldNode extends ExplicitFieldNode implements SimpleFieldNodeInterface
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        Identifier $key,
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
        return $this->key->toString();
    }
}
