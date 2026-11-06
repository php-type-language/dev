<?php

declare(strict_types=1);

namespace TypeLang\Type\WrappingTypeNode;

use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode;

/**
 * A type a `null` is allowed besides, written with a leading `?`.
 *
 * ```
 *  ?Some\Any
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<T>
 */
final class NullableTypeNode extends WrappingTypeNode {}
