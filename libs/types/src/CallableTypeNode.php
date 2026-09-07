<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Template\TemplateParameterListNode;

/**
 * Something callable, written as a name with a parameter list behind it.
 *
 * ```
 *  Closure<T of Some>(T, string ...$rest): T
 *  ^^^^^^^                                    the name
 *         ^^^^^^^^^^^                         the template parameters
 *                    ^^^^^^^^^^^^^^^^^^^^     the parameters
 *                                          ^  the return type
 * ```
 */
final class CallableTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $name,
        /**
         * Parameters a callable accepts. Unlike the ones below, the list is
         * never a {@see null}: A callable is written with its parentheses,
         * and an empty list is what stands inside an empty pair of them.
         *
         * ```
         *  callable()      // an empty list
         *  callable(int)   // a list of 1 parameter
         * ```
         */
        public CallableParameterListNode $parameters = new CallableParameterListNode(),
        /**
         * A return type of callable, or {@see null} in case of it is left
         * unsaid.
         *
         * ```
         *  callable()       // null
         *  callable(): int  // a type
         * ```
         */
        public ?TypeNode $type = null,
        /**
         * Template parameters a callable declares, or {@see null} in case of
         * it declares none.
         *
         * The list is never empty, since a `callable<>()` is no type.
         *
         * ```
         *  callable(T): T     // null
         *  callable<T>(T): T  // a list of 1 parameter
         * ```
         */
        public ?TemplateParameterListNode $templates = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
