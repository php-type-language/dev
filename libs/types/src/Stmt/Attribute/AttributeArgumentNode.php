<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Attribute;

use TypeLang\Type\Node;
use TypeLang\Type\Stmt\TypeStatement;

class AttributeArgumentNode extends Node
{
    public function __construct(
        public TypeStatement $value,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}
}
