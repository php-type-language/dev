<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PropertyTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Content\TypeReader;
use TypeLang\PhpDoc\Parser\Content\VariableNameReader;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@property`" tags.
 *
 * See {@see PropertyTag} for details about this tag.
 */
final class PropertyTagFactory implements TagFactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): PropertyTag
    {
        $stream = new Stream($tag, $content);
        $type = null;

        if (!\str_starts_with($stream->value, '$')) {
            $type = $stream->apply(new TypeReader($this->parser));
        }

        $variable = $stream->apply(new VariableNameReader());

        return new PropertyTag(
            name: $tag,
            type: $type,
            variable: $variable,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
