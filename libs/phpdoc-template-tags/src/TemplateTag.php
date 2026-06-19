<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\PHPDoc\Tag\OptionalTypeProviderInterface;
use TypeLang\PHPDoc\Tag\Tag;

/**
 * ```
 *
 * * @tempalte <name> ['of' <Type>] [<description>]
 * ```
 */
class TemplateTag extends Tag implements OptionalTypeProviderInterface
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $templateName
     */
    public function __construct(
        string $name,
        protected readonly string $templateName,
        protected readonly ?TypeStatement $type = null,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }

    public function getType(): ?TypeStatement
    {
        return $this->type;
    }

    /**
     * @return non-empty-string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }
}
