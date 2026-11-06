<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Node;

/**
 * Whatever stands in a slot of the `<...>` list of a type: an argument that
 * is passed or a parameter that is declared right there.
 *
 * ```
 *  Some\Any<int, *>
 *  //       ^^^  ^ two arguments
 *
 *  callable<T of Some>(T): T
 *  //       ^^^^^^^^^ a parameter
 * ```
 */
abstract class TemplateNode extends Node {}
