<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\NodeList;

/**
 * Template parameters a type declares, in the order they are written in.
 *
 * ```
 *  callable<T, U of Some>(T): U
 *  //       ^  ^^^^^^^^^
 *  //       two parameters
 * ```
 *
 * @template-extends NodeList<TemplateParameterNode>
 */
final class TemplateParameterListNode extends NodeList {}
