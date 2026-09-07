<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

/**
 * A field the key of which is a simple thing.
 *
 * A word or a scalar value is such a thing, while anything that has to be
 * read to be told from another is not.
 *
 * Such a key comes down to a string, so two of them are told apart by that
 * string alone. That is what a shape needs to refuse a key it already
 * carries.
 *
 * A key that is no simple thing offers no such string. See the
 * {@see ComplexFieldNode}.
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
