<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

/**
 * A field the key of which is a simple thing: A word or a scalar value, and
 * nothing that has to be read to be told from another.
 *
 * Such a key comes down to a string, so two of them are told apart by that
 * string alone - which is what a shape needs to refuse a key it already
 * carries. A key that is no simple thing (see {@see ComplexFieldNode}) offers
 * no such string, since whether two references denote the same constant is
 * beyond what a written statement says.
 */
interface SimpleFieldNodeInterface
{
    /**
     * Gets the key of a field as the string it comes down to.
     *
     * ```
     *  array{name: string}   // "name"
     *  array{"some key": T}  // "some key"
     *  array{42: T}          // "42"
     * ```
     *
     * @return non-empty-string
     */
    public function getIndex(): string;
}
