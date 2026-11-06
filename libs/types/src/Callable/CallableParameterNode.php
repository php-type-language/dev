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
 *
 * The constructor checks the parameter on creation only. The properties are
 * public and writable, so a later change is not checked. Since PHP 8.4,
 * property hooks make it possible to keep the checks permanent.
 */
final class CallableParameterNode extends Node
{
    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the parameter carries neither a type nor a name,
     *         or is both variadic and optional
     */
    public function __construct(
        /**
         * Type of parameter, or {@see null} in case of a parameter that is
         * built by hand and carries a name alone.
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
         * A `callable(bool $flag =)`. A variadic parameter is optional
         * itself, so the two flags are never both set.
         */
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        if ($type === null && $name === null) {
            throw new \InvalidArgumentException(
                'Required indication of the type or name of the parameter (one of)',
            );
        }

        if ($isVariadic && $isOptional) {
            throw new \InvalidArgumentException(
                'Parameter cannot be both variable and optional (variadic parameter is already optional)',
            );
        }

        parent::__construct($offset);
    }
}
