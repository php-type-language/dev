<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\TypeStatement;

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
