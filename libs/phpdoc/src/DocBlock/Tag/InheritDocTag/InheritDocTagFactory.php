<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\InheritDocTag;

use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@inheritDoc`" tags.
 *
 * See {@see InheritDocTag} for details about this tag.
 */
final class InheritDocTagFactory implements TagFactoryInterface
{
    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): InheritDocTag
    {
        $stream = new Stream($tag, $content);

        return new InheritDocTag(
            name: $tag,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
