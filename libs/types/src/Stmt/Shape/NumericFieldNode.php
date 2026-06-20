<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Shape;

use TypeLang\Node\Literal\IntLiteralNode;
use TypeLang\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Stmt\TypeStatement;

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
