<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Name;
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
final class TemplateParameterNode extends TemplateNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $name,
        /**
         * The end the parameter is bounded at from above, written with an
         * `of` or an `as`.
         *
         * The argument is to be a subtype of it.
         */
        public ?TemplateBoundNode $upper = null,
        /**
         * The end the parameter is bounded at from below, written with
         * a `super`.
         *
         * The argument is to be a supertype of it.
         */
        public ?TemplateBoundNode $lower = null,
        /**
         * The type the parameter takes when no argument is passed, written
         * with an `=`.
         *
         * It bounds nothing. A bound says what an argument may be, while
         * a default says what it becomes when left unsaid.
         */
        public ?TypeNode $default = null,
        /**
         * Declaration-site variance of the parameter, or {@see null} in case of
         * it carries none, which makes the parameter invariant.
         *
         * What the word means is left to whoever reads the tree. A `covariant`
         * or an `out` is an ordinary identifier as far as the tree goes.
         */
        public ?Name $variance = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
