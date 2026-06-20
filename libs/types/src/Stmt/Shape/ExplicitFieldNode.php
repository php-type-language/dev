<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Shape;

use TypeLang\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Stmt\TypeStatement;

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
        TypeStatement $type,
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
