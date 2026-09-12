<?php

declare(strict_types=1);

namespace TypeLang\Type\Callable;

use TypeLang\Type\NodeList;

/**
 * Parameters of a callable, in the order they are written in.
 *
 * ```
 *  callable(int, string ...$rest)
 *  //       ^^^  ^^^^^^^^^^^^^^^
 *  //       two parameters
 * ```
 *
 * @template-extends NodeList<CallableParameterNode>
 */
final class CallableParameterListNode extends NodeList {}
