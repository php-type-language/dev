<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Tag\TemplateExtendsTag;

use TypeLang\PHPDoc\DocBlock\Tag\Tag;
use TypeLang\PHPDoc\DocBlock\Tag\TypeProviderInterface;
use TypeLang\Type\Stmt\TypeStatement;

abstract class TemplateInheritanceTag extends Tag implements TypeProviderInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        public readonly TypeStatement $type,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
