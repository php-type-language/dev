<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

/**
 * @template T of TypeNode = TypeNode
 * @template-extends WrappingTypeNode<T>
 */
class TypeOffsetAccessNode extends WrappingTypeNode
{
    public function __construct(
        TypeNode $type,
        public readonly TypeNode $access,
    ) {
        parent::__construct($type);
    }
}
