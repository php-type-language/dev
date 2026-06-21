<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\VarTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\OptionalVariableNameReader;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Content\TypeReader;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@var`" tags.
 *
 * See {@see VarTag} for details about this tag.
 */
final class VarTagFactory implements TagFactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): VarTag
    {
        $stream = new Stream($tag, $content);

        $type = $stream->apply(new TypeReader($this->parser));
        $variable = $stream->apply(new OptionalVariableNameReader());

        return new VarTag(
            name: $tag,
            type: $type,
            variable: $variable,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
