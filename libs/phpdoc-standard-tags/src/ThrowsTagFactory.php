<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Standard;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@throws`" tags.
 *
 * See {@see ThrowsTag} for details about this tag.
 */
final class ThrowsTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(
            tolerant: true,
            shapes: false,
            callables: false,
            literals: false,
            generics: false,
            list: false,
        ),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): ThrowsTag
    {
        return new ThrowsTag(
            name: $name,
            type: $content->nextType($name, $this->parser),
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
