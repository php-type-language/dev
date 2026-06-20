<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Attribute;

use TypeLang\Node\Node;
use TypeLang\Node\Stmt\TypeStatement;

class AttributeArgumentNode extends Node
{
    public function __construct(
        public TypeStatement $value,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}
}
