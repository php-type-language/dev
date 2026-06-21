<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<Identifier>
 */
final class NamedFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => $this->key->toString();
    }

    public function __construct(
        Identifier $key,
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
