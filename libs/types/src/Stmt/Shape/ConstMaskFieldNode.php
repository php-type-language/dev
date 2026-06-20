<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\ConstMaskNode;
use TypeLang\Type\Stmt\TypeStatement;

/**
 * @template-extends ExplicitFieldNode<ConstMaskNode>
 */
class ConstMaskFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => (string) $this->key;
    }

    public function __construct(
        ConstMaskNode $key,
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
