<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\TypeStatement;

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
        TypeStatement $type,
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
