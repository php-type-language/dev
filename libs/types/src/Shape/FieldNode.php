<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * A single field of a shape.
 *
 * A field is written either with a key or with none, and a shape may mix
 * the two. The keyless ones are numbered from zero in the order they are
 * written in.
 *
 * ```
 *  array{name: string, id: int}  // fields written with a key
 *  array{string, int}            // fields written with none
 *  array{string, id: int}        // both in one shape
 * ```
 */
abstract class FieldNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $type,
        /**
         * Whether a field may be absent, written as a `?` in front of
         * the colon.
         *
         * ```
         *  array{name: string}   // false
         *  array{name?: string}  // true
         * ```
         */
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
