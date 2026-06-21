<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\TemplateTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\IdentifierReader;
use TypeLang\PhpDoc\Parser\Content\OptionalTypeReader;
use TypeLang\PhpDoc\Parser\Content\OptionalValueReader;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@template`" tags.
 *
 * See {@see TemplateTag} for details about this tag.
 */
final class TemplateTagFactory implements TagFactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): TemplateTag
    {
        $stream = new Stream($tag, $content);

        $template = $stream->apply(new IdentifierReader());

        $type = null;

        $stream->lookahead(function (Stream $stream) use (&$type): bool {
            if ($stream->apply(new OptionalValueReader('of')) !== null) {
                $type = $stream->apply(new OptionalTypeReader($this->parser));
            }

            return $type !== null;
        });

        return new TemplateTag(
            name: $tag,
            template: $template,
            type: $type,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
