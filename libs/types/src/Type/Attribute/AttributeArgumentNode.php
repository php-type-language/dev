<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Attribute;

use TypeLang\Node\Node;
use TypeLang\Node\Type\TypeNode;

class AttributeArgumentNode extends Node
{
    public function __construct(
        public TypeNode $value,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}
}
