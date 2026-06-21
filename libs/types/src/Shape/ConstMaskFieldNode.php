<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<ConstMaskNode>
 */
final class ConstMaskFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => (string) $this->key;
    }

    public function __construct(
        ConstMaskNode $key,
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
