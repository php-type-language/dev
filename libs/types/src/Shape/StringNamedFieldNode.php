<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<StringLiteralNode>
 */
final class StringNamedFieldNode extends ExplicitFieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        StringLiteralNode $key,
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
        return $this->key->value;
    }
}
