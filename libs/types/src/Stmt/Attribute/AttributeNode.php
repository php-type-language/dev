<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Attribute;

use TypeLang\Type\Name;
use TypeLang\Type\Node;

class AttributeNode extends Node
{
    public function __construct(
        public Name $name,
        public ?AttributeArgumentsListNode $arguments = null,
    ) {}
}
