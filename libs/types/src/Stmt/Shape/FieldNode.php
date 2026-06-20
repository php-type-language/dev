<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Node;
use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\TypeStatement;

abstract class FieldNode extends Node implements \Stringable
{
    public function __construct(
        public TypeStatement $type,
        public bool $optional = false,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}

    public function __toString(): string
    {
        return $this->optional ? 'optional' : 'required';
    }
}
