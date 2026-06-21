<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Template;

use TypeLang\Node\Identifier;
use TypeLang\Node\Node;
use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\TypeNode;

class TemplateArgumentNode extends Node
{
    public ?Identifier $hint;

    /**
     * @param Identifier|non-empty-string|null $hint
     */
    public function __construct(
        public TypeNode $value,
        Identifier|string|null $hint = null,
        public ?AttributeGroupsListNode $attributes = null,
    ) {
        $this->hint = \is_string($hint) ? new Identifier($hint) : $hint;
    }
}
