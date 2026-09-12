<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

/**
 * A field of a shape written with no key at all, the way an element of
 * a plain list is.
 *
 * ```
 *  array{int, string}
 *  //    ^^^  ^^^^^^ two implicit fields
 * ```
 */
final class ImplicitFieldNode extends FieldNode {}
