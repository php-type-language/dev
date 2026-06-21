<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\TypeNode;

/**
 * @template TKey of mixed
 */
abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * Gets a pretty-printed string representation of the key
     */
    abstract public string $index {
        get;
    }

    public function __construct(
        /**
         * @var TKey
         */
        public mixed $key,
        TypeNode $type,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct(
            type: $type,
            optional: $optional,
            attributes: $attributes,
        );
    }
}
