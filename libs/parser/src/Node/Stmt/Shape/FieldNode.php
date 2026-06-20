<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

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
