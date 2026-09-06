<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<Identifier>
 */
final class NamedFieldNode extends ExplicitFieldNode
{
    public function __construct(
        Identifier $key,
        TypeNode $type,
        bool $isOptional = false,
        ?AttributeGroupListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            attributes: $attributes,
        );
    }

    public function getIndex(): string
    {
        return $this->key->toString();
    }
}
