<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@use`" tags.
 *
 * See {@see TemplateUseTag} for details about this tag.
 */
final class TemplateUseTagFactory implements TagFactoryInterface
{
    private readonly TemplateExtendsTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new TemplateExtendsTagFactory($parser);
    }

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): TemplateUseTag
    {
        $result = $this->factory->create($tag, $content, $descriptions);

        return new TemplateUseTag(
            name: $result->name,
            type: $result->type,
            description: $result->description,
        );
    }
}
