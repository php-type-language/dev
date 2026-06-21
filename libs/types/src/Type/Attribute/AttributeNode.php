<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Attribute;

use TypeLang\Node\Name;
use TypeLang\Node\Node;

class AttributeNode extends Node
{
    public function __construct(
        public Name $name,
        public ?AttributeArgumentsListNode $arguments = null,
    ) {}
}
