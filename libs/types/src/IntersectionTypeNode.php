<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * All of several types at once.
 *
 * ```
 *  Some\Any & Stringable
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends LogicalTypeNode<T>
 */
final class IntersectionTypeNode extends LogicalTypeNode {}
