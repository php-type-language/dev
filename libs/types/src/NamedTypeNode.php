<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Template\TemplateArgumentsListNode;

final class NamedTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public ?TemplateArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {}
}
