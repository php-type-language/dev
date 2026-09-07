<?php

declare(strict_types=1);

namespace TypeLang\Type\Callable;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\VariableNode;

/**
 * A single parameter of a callable.
 *
 * ```
 *  callable(int &$byRef, string ...$rest, bool $flag = )
 *  //       ^^^^^^^^^^^  ^^^^^^^^^^^^^^^  ^^^^^^^^^^^^^
 *  //       by reference  variadic         optional
 * ```
 */
final class CallableParameterNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * Type of parameter. A statement always writes one, so a
         * {@see null} only ever reaches a tree that is built by hand, where
         * a name alone is enough to tell one parameter from another.
         */
        public ?TypeNode $type = null,
        /**
         * Name of a parameter, or {@see null} in case of it is written
         * without one.
         *
         * ```
         *  callable(int)          // null
         *  callable(int $value)   // a name
         * ```
         */
        public ?VariableNode $name = null,
        /**
         * Whether a parameter is taken by reference.
         *
         * It is written as an `&` behind the type, like the one of
         * a `callable(int &$byRef)`.
         */
        public bool $isOutput = false,
        /**
         * Whether a parameter takes every argument left, written as a `...`:
         * A `callable(string ...$rest)`.
         */
        public bool $isVariadic = false,
        /**
         * Whether a parameter may be left out, written as a trailing `=`:
         * A `callable(bool $flag =)`. A variadic one is optional already, so
         * the two are never both set.
         */
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct($offset);

        \assert($type !== null || $name !== null, new \TypeError(
            'Required indication of the type or name of the parameter (one of)',
        ));

        \assert($isVariadic === false || $isOptional === false, new \TypeError(
            'Parameter cannot be both variable and optional (variadic parameter is already optional)',
        ));
    }

    /**
     * Returns {@see true} in case of the parameter is an instance of the
     * passed class.
     *
     * @param class-string $class
     */
    public function is(string $class): bool
    {
        return $this instanceof $class;
    }
}
