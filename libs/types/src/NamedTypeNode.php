<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Template\TemplateArgumentListNode;

final class NamedTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public ?TemplateArgumentListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {}
}
