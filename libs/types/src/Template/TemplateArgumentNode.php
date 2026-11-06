<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

/**
 * A value passed to a template parameter, as opposed to a parameter
 * declared by a type.
 *
 * ```
 *  Some\Any<int, covariant string, *>
 *  //       ^^^  ^^^^^^^^^^^^^^^^  ^ three arguments
 * ```
 *
 * @see TypedTemplateArgumentNode
 * @see WildcardTemplateArgumentNode
 */
abstract class TemplateArgumentNode extends TemplateNode {}
