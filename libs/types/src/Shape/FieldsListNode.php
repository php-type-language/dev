<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\NodeList;

/**
 * The fields of a shape, in the order they are written in.
 *
 * An empty list is a shape written with no fields at all, which is a thing of
 * its own: An `array` carries no list, while an `array{}` carries an empty one
 * (see {@see \TypeLang\Type\NamedTypeNode::$fields}).
 *
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
        /**
         * Whether the shape is written with the fields it lists and no other.
         * An unsealed one ends in a `...`, which says that whatever else it
         * carries was left unsaid.
         *
         * ```
         *  array{a: int}       // sealed
         *  array{a: int, ...}  // unsealed
         * ```
         */
        public bool $isSealed = true,
        int $offset = 0,
    ) {
        parent::__construct($list, $offset);
    }
}
