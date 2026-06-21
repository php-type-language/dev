<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag;

use TypeLang\PhpDoc\DocBlock\Tag\Tag;
use TypeLang\PhpDoc\DocBlock\Tag\TypeProviderInterface;
use TypeLang\Type\TypeNode;

abstract class TemplateInheritanceTag extends Tag implements TypeProviderInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        public readonly TypeNode $type,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
