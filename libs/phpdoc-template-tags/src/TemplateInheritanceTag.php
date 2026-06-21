<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\Node\Type\TypeNode;
use TypeLang\PHPDoc\Tag\Tag;
use TypeLang\PHPDoc\Tag\TypeProviderInterface;

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
