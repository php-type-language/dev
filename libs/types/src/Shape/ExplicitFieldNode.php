<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Identifier;
use TypeLang\Type\TypeNode;

/**
 * A field of a shape written with a key of its own.
 *
 * What the key may be is what tells the children of this class apart.
 * A {@see NamedFieldNode} carries a bare word, a {@see ScalarFieldNode}
 * a scalar value and a {@see ComplexFieldNode} a reference that has to
 * be read.
 *
 * @template TKey of Identifier|TypeNode
 */
abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The key a field is written with.
         *
         * @var TKey
         */
        public Identifier|TypeNode $key,
        TypeNode $type,
        /**
         * Whether a field may be absent, written as a `?` in front of
         * the colon. Only a field with a key can be marked so.
         *
         * ```
         *  array{name: string}   // false
         *  array{name?: string}  // true
         * ```
         */
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct($type, $offset);
    }
}
