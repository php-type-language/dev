<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\Literal\StringLiteralNode;
use TypeLang\Node\Type\TypeNode;

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
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            optional: $optional,
            attributes: $attributes,
        );
    }
}
