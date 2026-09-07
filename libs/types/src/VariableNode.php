<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A variable, written as a name with a `$` in front of it.
 *
 * ```
 *  callable(int $value): void
 *  //           ^^^^^^ names a parameter
 *
 *  ($value is int ? string : bool)
 *  //^^^^^ stands as the subject of a condition
 * ```
 *
 * A variable is no type: It names a place a value is kept in, and the type
 * of what is kept there is what a statement is about. The one variable that
 * is a type of its own is the `$this`, and that one is a {@see ThisNode}.
 */
final class VariableNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The name of the variable, written without the `$` it begins with.
         */
        public Identifier $name,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
