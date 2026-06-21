<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Template;

use TypeLang\PhpDoc\Tag\Tag;
use TypeLang\PhpDoc\Tag\TypeProviderInterface;
use TypeLang\Type\TypeNode;

abstract class TemplateInheritanceTag extends Tag implements TypeProviderInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        protected readonly TypeNode $type,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }

    public function getType(): TypeNode
    {
        return $this->type;
    }
}
