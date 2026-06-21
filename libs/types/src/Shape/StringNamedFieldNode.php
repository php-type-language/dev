<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<StringLiteralNode>
 */
final class StringNamedFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => $this->key->value;
    }

    public function __construct(
        StringLiteralNode $key,
        TypeNode $type,
        bool $optional = false,
        ?AttributeGroupListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            optional: $optional,
            attributes: $attributes,
        );
    }
}
