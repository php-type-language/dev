<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\CopyrightTag;

use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@copyright`" tags.
 *
 * See {@see CopyrightTag} for details about this tag.
 */
final class CopyrightTagFactory implements TagFactoryInterface
{
    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): CopyrightTag
    {
        $stream = new Stream($tag, $content);

        return new CopyrightTag(
            name: $tag,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
