<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 * @template-extends WrappingTypeNode<T>
 */
final class TypesListNode extends WrappingTypeNode {}
