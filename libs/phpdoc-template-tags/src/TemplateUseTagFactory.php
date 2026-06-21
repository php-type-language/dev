<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Template;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@use`"
 * or "`@template-use`" tags.
 *
 * See {@see TemplateUseTag} for details about this tag.
 */
final class TemplateUseTagFactory implements FactoryInterface
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
