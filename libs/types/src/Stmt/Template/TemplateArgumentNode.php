<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\TypeStatement;

class TemplateArgumentNode extends Node
{
    public ?Identifier $hint;

    /**
     * @param Identifier|non-empty-string|null $hint
     */
    public function __construct(
        public TypeStatement $value,
        Identifier|string|null $hint = null,
        public ?AttributeGroupsListNode $attributes = null,
    ) {
        $this->hint = \is_string($hint) ? new Identifier($hint) : $hint;
    }
}
