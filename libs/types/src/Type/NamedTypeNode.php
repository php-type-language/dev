<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

use TypeLang\Node\Name;
use TypeLang\Node\Type\Shape\FieldsListNode;
use TypeLang\Node\Type\Template\TemplateArgumentsListNode;

class NamedTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public ?TemplateArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {}
}
