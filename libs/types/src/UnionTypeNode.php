<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * Any one of several types.
 *
 * ```
 *  int|string|null
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends LogicalTypeNode<T>
 */
final class UnionTypeNode extends LogicalTypeNode {}
