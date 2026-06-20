<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\PHPDoc\Tag\Tag;
use TypeLang\PHPDoc\Tag\TypeProviderInterface;
use TypeLang\Type\Stmt\TypeStatement;

abstract class TemplateInheritanceTag extends Tag implements TypeProviderInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        protected readonly TypeStatement $type,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }

    public function getType(): TypeStatement
    {
        return $this->type;
    }
}
