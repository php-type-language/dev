<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Template;

use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@template`" tags.
 *
 * See {@see TemplateTag} for details about this tag.
 */
final class TemplateTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): TemplateTag
    {
        $template = $content->nextIdentifier($name);

        $type = null;

        $content->transactional(function (Content $content) use (&$type): bool {
            if ($content->nextOptionalValue('of') !== null) {
                $type = $content->nextOptionalType($this->parser);
            }

            return $type !== null;
        });

        return new TemplateTag(
            name: $name,
            templateName: $template,
            type: $type,
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
