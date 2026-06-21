<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 * @template-extends WrappingTypeNode<T>
 */
final class TypeOffsetAccessNode extends WrappingTypeNode
{
    /**
     * @param T $type
     */
    public function __construct(
        TypeNode $type,
        public readonly TypeNode $access,
    ) {
        parent::__construct($type);
    }
}
