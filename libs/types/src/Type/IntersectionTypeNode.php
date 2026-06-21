<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

/**
 * @template T of TypeNode = TypeNode
 * @template-extends LogicalTypeNode<T>
 */
class IntersectionTypeNode extends LogicalTypeNode {}
