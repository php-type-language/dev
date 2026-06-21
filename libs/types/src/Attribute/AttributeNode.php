<?php

declare(strict_types=1);

namespace TypeLang\Type\Attribute;

use TypeLang\Type\Name;
use TypeLang\Type\Node;

final class AttributeNode extends Node
{
    public function __construct(
        public Name $name,
        public ?AttributeArgumentsListNode $arguments = null,
    ) {}
}
