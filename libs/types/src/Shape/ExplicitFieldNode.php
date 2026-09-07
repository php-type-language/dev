<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\TypeNode;

/**
 * A field of a shape written with a key of its own.
 *
 * What the key may be is what tells the children of this class apart.
 * A {@see NamedFieldNode} carries a bare word, a {@see ScalarFieldNode}
 * a scalar value and a {@see ComplexFieldNode} a reference that has to
 * be read.
 *
 * A key that comes down to a string of its own is marked by the
 * {@see SimpleFieldNodeInterface}. That string is what a shape tells its
 * keys apart by.
 *
 * @template TKey of mixed
 */
abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The key a field is written with. Its kind is what the children of
         * this class differ in, so a child narrows it down to a node of its
         * own.
         *
         * @var TKey
         */
        public mixed $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct(
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }
}
