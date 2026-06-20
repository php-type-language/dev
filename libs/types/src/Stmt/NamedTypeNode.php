<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\Stmt\Shape\FieldsListNode;
use TypeLang\Type\Stmt\Template\TemplateArgumentsListNode;

class NamedTypeNode extends TypeStatement
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
