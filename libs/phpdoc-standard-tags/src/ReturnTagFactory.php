<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Standard;

use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@return`" tags.
 *
 * See {@see ReturnTag} for details about this tag.
 */
final class ReturnTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): ReturnTag
    {
        return new ReturnTag(
            name: $name,
            type: $content->nextType($name, $this->parser),
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
