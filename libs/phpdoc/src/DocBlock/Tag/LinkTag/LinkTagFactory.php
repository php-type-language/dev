<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\LinkTag;

use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\ElementReferenceReader;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Content\UriReferenceReader;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@link`" tags.
 *
 * See {@see LinkTag} for details about this tag.
 */
final class LinkTagFactory implements TagFactoryInterface
{
    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): LinkTag
    {
        $stream = new Stream($tag, $content);

        try {
            $reference = $stream->apply(new UriReferenceReader());
        } catch (\Throwable) {
            $reference = $stream->apply(new ElementReferenceReader());
        }

        return new LinkTag(
            name: $tag,
            reference: $reference,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
