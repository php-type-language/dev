<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<IntLiteralNode>
 */
final class NumericFieldNode extends ExplicitFieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        IntLiteralNode $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }

    public function getIndex(): string
    {
        return (string) $this->key->value;
    }
}
