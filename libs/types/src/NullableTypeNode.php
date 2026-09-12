<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A type a `null` is allowed besides, written with a leading `?`.
 *
 * ```
 *  ?Some\Any
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<TypeNode>
 */
final class NullableTypeNode extends WrappingTypeNode {}
