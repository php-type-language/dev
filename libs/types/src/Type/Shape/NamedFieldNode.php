<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Identifier;
use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\TypeNode;

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
