<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 */
abstract class WrappingTypeNode extends TypeNode
{
    /**
     * @param T $type
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $type,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
