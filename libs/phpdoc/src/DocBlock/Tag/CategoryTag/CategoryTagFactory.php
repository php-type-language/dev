<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\CategoryTag;

use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@category`" tags.
 *
 * See {@see CategoryTag} for details about this tag.
 */
final class CategoryTagFactory implements TagFactoryInterface
{
    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): CategoryTag
    {
        $stream = new Stream($tag, $content);

        return new CategoryTag(
            name: $tag,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
