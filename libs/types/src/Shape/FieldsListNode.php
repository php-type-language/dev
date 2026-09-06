<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\NodeList;

/**
 * @template-extends NodeList<FieldNode>
 */
final class FieldsListNode extends NodeList
{
    /**
     * @param list<FieldNode> $list
     */
    public function __construct(
        array $list = [],
        public bool $isSealed = true,
    ) {
        parent::__construct($list);
    }
}
