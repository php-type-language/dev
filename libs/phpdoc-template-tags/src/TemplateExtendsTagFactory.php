<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Template;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@extends`"
 * or "`@template-extends`" tags.
 *
 * See {@see TemplateExtendsTag} for details about this tag.
 */
final class TemplateExtendsTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): TemplateExtendsTag
    {
        $type = $content->nextType($name, $this->parser);

        return new TemplateExtendsTag(
            name: $name,
            type: $type,
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
