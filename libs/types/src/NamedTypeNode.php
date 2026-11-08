<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Template\TemplateListNode;

/**
 * A type referenced by name, along with whatever the name is followed by.
 *
 * The meaning of what follows is left to whoever reads the tree, as it depends
 * on the type the name stands for. A `FOO` may be a constant or a class, and
 * the template arguments of an `array{a: int, ...<string, int>}` are not the
 * ones of an `array<int>`.
 *
 * ```
 *  Some\Any<int, string>
 *  ^^^^^^^^              the name
 *          ^^^^^^^^^^^^^ the template arguments
 *
 *  array{name: string}
 *  ^^^^^               the name
 *       ^^^^^^^^^^^^^^ the shape fields
 *
 * object{name: string, ...<string, mixed>}
 * ^^^^^^                                   the name
 *       ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ the shape fields
 *                         ^^^^^^^^^^^^^^^  the template arguments
 * ```
 */
final class NamedTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $name,
        /**
         * Template arguments of a type, or {@see null} in case of the name is
         * followed by none.
         *
         * The list is never empty.
         *
         * ```
         *  Some       // null
         *  Some<int>  // a list of 1 argument
         * ```
         */
        public ?TemplateListNode $arguments = null,
        /**
         * Shape fields of a type, or {@see null} in case of the name is
         * followed by none. An empty list is not the same as a {@see null}:
         * A shape may well be written with no fields at all.
         *
         * ```
         *  array           // null: no shape
         *  array{}         // an empty list: a shape of no fields
         *  array{a: int}   // a list of 1 field
         * ```
         */
        public ?FieldListNode $fields = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
