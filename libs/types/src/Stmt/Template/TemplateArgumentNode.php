<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt\Template;

use TypeLang\Node\Identifier;
use TypeLang\Node\Node;
use TypeLang\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Stmt\TypeStatement;

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
