<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<TypeNode>
 */
final class NullableTypeNode extends WrappingTypeNode {}
