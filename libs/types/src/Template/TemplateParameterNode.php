<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * A template parameter declared by a type.
 *
 * A parameter is a name the type introduces, along with the limits put on it.
 * Each limit is written at most once and any of them may be left out.
 *
 * ```
 *  callable<T, U of Some super Any = int>(T): U
 *  //       ^                              a name and nothing else
 *  //          ^^^^^^^^^                   the upper bound
 *  //                    ^^^^^^^^^         the lower bound
 *  //                              ^^^^^   the default
 * ```
 */
final class TemplateParameterNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Identifier $name,
        /**
         * The end the parameter is bounded at from above, written with an
         * `of` or an `as`.
         *
         * The argument is to be a subtype of it.
         */
        public ?TemplateBoundEdgeNode $upper = null,
        /**
         * The end the parameter is bounded at from below, written with
         * a `super`.
         *
         * The argument is to be a supertype of it.
         */
        public ?TemplateBoundEdgeNode $lower = null,
        /**
         * The type the parameter takes when no argument is passed, written
         * with an `=`.
         *
         * It bounds nothing. A bound says what an argument may be, while
         * a default says what it becomes when left unsaid.
         */
        public ?TypeNode $default = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
