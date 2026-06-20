<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Shape;

use TypeLang\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Stmt\ConstMaskNode;
use TypeLang\Node\Stmt\TypeStatement;

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
