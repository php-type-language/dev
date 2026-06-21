<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ApiTag;

use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@api`" tags.
 *
 * See {@see ApiTag} for details about this tag.
 */
final class ApiTagFactory implements TagFactoryInterface
{
    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): ApiTag
    {
        $stream = new Stream($tag, $content);

        return new ApiTag(
            name: $tag,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
