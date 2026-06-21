<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Node;
use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\TypeNode;

abstract class FieldNode extends Node implements \Stringable
{
    public function __construct(
        public TypeNode $type,
        public bool $optional = false,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}

    public function __toString(): string
    {
        return $this->optional ? 'optional' : 'required';
    }
}
