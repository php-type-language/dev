<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TemplateBoundNode\BoundKind;
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
     * @throws \InvalidArgumentException in case of the upper bound is not an upper one or the lower bound is not a lower one
     */
    public function __construct(
        public Identifier $name,
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
         */
        public ?VarianceNode $variance = null,
        int $offset = 0,
    ) {
        if ($upper !== null && $upper->kind !== BoundKind::Upper) {
            throw new \InvalidArgumentException('The upper bound of a template parameter must be an upper one');
        }

        if ($lower !== null && $lower->kind !== BoundKind::Lower) {
            throw new \InvalidArgumentException('The lower bound of a template parameter must be a lower one');
        }

        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->name = clone $this->name;

        if ($this->upper !== null) {
            $this->upper = clone $this->upper;
        }

        if ($this->lower !== null) {
            $this->lower = clone $this->lower;
        }

        if ($this->default !== null) {
            $this->default = clone $this->default;
        }

        if ($this->variance !== null) {
            $this->variance = clone $this->variance;
        }
    }
}
