<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 *
 * @template-extends WrappingTypeNode<T>
 */
final class TypeOffsetAccessNode extends WrappingTypeNode
{
    /**
     * @param T $type
     * @param int<0, max> $offset
     */
    public function __construct(
        TypeNode $type,
        public readonly TypeNode $access,
        int $offset = 0,
    ) {
        parent::__construct($type, $offset);
    }
}
