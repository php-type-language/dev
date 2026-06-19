<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@implements`"
 * or "`@template-implements`" tags.
 *
 * See {@see TemplateImplementsTag} for details about this tag.
 */
final class TemplateImplementsTagFactory implements FactoryInterface
{
    private readonly TemplateExtendsTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new TemplateExtendsTagFactory($parser);
    }

    public function create(
        string $name,
        Content $content,
        DescriptionParserInterface $descriptions,
    ): TemplateImplementsTag {
        $tag = $this->factory->create($name, $content, $descriptions);

        return new TemplateImplementsTag(
            name: $tag->getName(),
            type: $tag->getType(),
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
