<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

abstract class FieldNode extends Node implements \Stringable
{
    public function __construct(
        public TypeNode $type,
        public bool $optional = false,
        public ?AttributeGroupListNode $attributes = null,
    ) {}

    public function __toString(): string
    {
        return $this->optional ? 'optional' : 'required';
    }
}
