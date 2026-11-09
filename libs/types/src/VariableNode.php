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
 */
final class VariableNode extends Node
{
    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the name is empty
     */
    public function __construct(
        /**
         * The name of the variable, written without the leading `$`.
         *
         * @var non-empty-string
         */
        public string $name,
        int $offset = 0,
    ) {
        if ($name === '') {
            throw new \InvalidArgumentException('Variable name cannot be empty');
        }

        parent::__construct($offset);
    }
}
