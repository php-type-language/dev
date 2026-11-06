<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * The type of the object a method is called on, written as a `$this`.
 *
 * ```
 *  callable(): $this
 * ```
 *
 * It is written the way a variable is, but it is a type. Any other variable
 * is a {@see VariableNode} and no type at all.
 */
final class ThisTypeNode extends TypeNode {}
