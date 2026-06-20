<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Shape;

use TypeLang\Node\Node;
use TypeLang\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Stmt\TypeStatement;

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
