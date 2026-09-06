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
     * @param int<0, max> $offset
     */
    public function __construct(
        array $list = [],
        public bool $isSealed = true,
        int $offset = 0,
    ) {
        parent::__construct($list, $offset);
    }
}
