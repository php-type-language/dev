<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Standard;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@property`" tags.
 *
 * See {@see PropertyTag} for details about this tag.
 */
final class PropertyTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): PropertyTag
    {
        $type = null;

        if (!\str_starts_with($content->value, '$')) {
            $type = $content->nextType($name, $this->parser);
        }

        $variable = $content->nextVariable($name);

        return new PropertyTag(
            name: $name,
            type: $type,
            variable: $variable,
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
