<?php

declare(strict_types=1);

namespace TypeLang\Type\Attribute;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

final class AttributeArgumentNode extends Node
{
    public function __construct(
        public TypeNode $value,
        public ?AttributeGroupListNode $attributes = null,
    ) {}
}
