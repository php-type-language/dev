<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@template-covariant`" tags.
 *
 * See {@see TemplateCovariantTag} for details about this tag.
 */
final class TemplateCovariantTagFactory implements FactoryInterface
{
    private readonly TemplateTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new TemplateTagFactory($parser);
    }

    public function create(
        string $name,
        Content $content,
        DescriptionParserInterface $descriptions,
    ): TemplateCovariantTag {
        $tag = $this->factory->create($name, $content, $descriptions);

        return new TemplateCovariantTag(
            name: $tag->getName(),
            templateName: $tag->getTemplateName(),
            type: $tag->getType(),
            description: $tag->getDescription(),
        );
    }
}
