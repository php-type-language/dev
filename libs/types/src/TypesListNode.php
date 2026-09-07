<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A list of the wrapped type, written in the legacy `[]` notation.
 *
 * ```
 *  Some\Any[]
 *  ^^^^^^^^   the type the list is made of
 *
 *  Some\Any[][]
 *  ^^^^^^^^^^   a list the outer list is made of
 * ```
 *
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<T>
 */
final class TypesListNode extends WrappingTypeNode {}
