<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

final class TemplateArgumentNode extends Node
{
    public ?Identifier $hint;

    public function __construct(
        public TypeNode $value,
        Identifier|null $hint = null,
        public ?AttributeGroupsListNode $attributes = null,
    ) {
        $this->hint = $hint;
    }
}
