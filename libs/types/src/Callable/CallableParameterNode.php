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
 *  callable(int &$byRef, string ...$rest, bool $flag = true)
 *  //       ^^^^^^^^^^^  ^^^^^^^^^^^^^^^  ^^^^^^^^^^^^^^^^
 *  //       by reference  variadic         optional, with a default
 * ```
 *
 * A parameter is optional when it has a {@see DefaultValueNode}, with a value
 * or without one.
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
     *         or is both variadic and has a default
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
         * Default of a parameter, or {@see null} in case of a required one.
         * A variadic parameter is optional itself, so it has no default.
         *
         * ```
         *  callable(int $a)            // null
         *  callable(int $a =)          // a default without a value
         *  callable(int $a = 42)       // a default with a value
         * ```
         */
        public ?DefaultValueNode $default = null,
        int $offset = 0,
    ) {
        if ($type === null && $name === null) {
            throw new \InvalidArgumentException(
                'Required indication of the type or name of the parameter (one of)',
            );
        }

        if ($isVariadic && $default !== null) {
            throw new \InvalidArgumentException(
                'Parameter cannot be both variable and have a default (variadic parameter is already optional)',
            );
        }

        parent::__construct($offset);
    }

    public function __clone()
    {
        if ($this->type !== null) {
            $this->type = clone $this->type;
        }

        if ($this->name !== null) {
            $this->name = clone $this->name;
        }

        if ($this->default !== null) {
            $this->default = clone $this->default;
        }
    }
}
