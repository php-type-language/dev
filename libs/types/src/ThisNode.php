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
 * It is written the way a variable is, but it is a type. It names the object
 * itself rather than a place a value is kept in.
 *
 * Any other variable is a {@see VariableNode} and no type at all.
 */
final class ThisNode extends TypeNode {}
