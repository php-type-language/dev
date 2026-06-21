<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

/**
 * @template T of TypeNode = TypeNode
 */
abstract class WrappingTypeNode extends TypeNode
{
    /**
     * @param T $type
     */
    public function __construct(
        public TypeNode $type,
    ) {}
}
