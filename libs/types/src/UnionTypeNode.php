<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 * @template-extends LogicalTypeNode<T>
 */
final class UnionTypeNode extends LogicalTypeNode {}
