<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

use TypeLang\Node\Identifier;
use TypeLang\Node\Name;
use TypeLang\Node\Type\Shape\FieldsListNode;
use TypeLang\Node\Type\Template\TemplateArgumentsListNode;

class NamedTypeNode extends TypeNode
{
    public Name $name;

    /**
     * @param Name|Identifier|non-empty-string $name
     */
    public function __construct(
        Name|Identifier|string $name,
        public ?TemplateArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {
        $this->name = $name instanceof Name ? $name : new Name($name);
    }
}
