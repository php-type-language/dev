<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Content\TypeReader;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@throws`" tags.
 *
 * See {@see ThrowsTag} for details about this tag.
 */
final class ThrowsTagFactory implements TagFactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): ThrowsTag
    {
        $stream = new Stream($tag, $content);

        return new ThrowsTag(
            name: $tag,
            type: $stream->apply(new TypeReader($this->parser)),
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
