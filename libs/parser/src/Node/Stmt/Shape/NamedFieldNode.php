<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

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
