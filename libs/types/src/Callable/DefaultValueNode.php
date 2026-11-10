<?php

declare(strict_types=1);

namespace TypeLang\Type\Callable;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * The default of a callable parameter, written as an `=` and the value it
 * stands for.
 *
 * The node itself tells that the parameter may be left out, so a parameter
 * with it is optional, and one without it is required.
 *
 * ```
 *  callable(int $a =, string $b = 'x')
 *  //             ^                      no value, the parameter is optional
 *  //                            ^^^^^^^ the value
 * ```
 */
final class DefaultValueNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The value of a default, or {@see null} in case of the parameter
         * is only marked as optional.
         *
         * The value is a literal or a constant, so it is a type node of the
         * same kind as in any other position.
         */
        public ?TypeNode $value = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        if ($this->value !== null) {
            $this->value = clone $this->value;
        }
    }
}
