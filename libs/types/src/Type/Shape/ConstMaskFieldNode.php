<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\ConstMaskNode;
use TypeLang\Node\Type\TypeNode;

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
