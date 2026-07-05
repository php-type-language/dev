<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<IntLiteralNode>
 */
final class NumericFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => (string) $this->key->value;
    }

    public function __construct(
        IntLiteralNode $key,
        TypeNode $type,
        bool $isOptional = false,
        ?AttributeGroupListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            attributes: $attributes,
        );
    }
}
