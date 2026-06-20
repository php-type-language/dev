<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Identifier;
use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\TypeStatement;

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
