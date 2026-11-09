<?php

declare(strict_types=1);

namespace TypeLang\Type\Template\TemplateBoundNode;

/**
 * The end a template parameter is bounded at. The value of a case is the word
 * the bound is written with, `as` is the other spelling of `of`.
 */
enum BoundKind: string
{
    /**
     * The bound from above: the argument is to be a subtype of the type,
     * written with an `of` or an `as`.
     *
     * ```
     *  callable<T of Some>(T): T
     * ```
     */
    case Upper = 'of';

    /**
     * The bound from below: the argument is to be a supertype of the type,
     * written with a `super`.
     *
     * ```
     *  callable<T super Any>(T): T
     * ```
     */
    case Lower = 'super';
}
