<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Standard;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@var`" tags.
 *
 * See {@see VarTag} for details about this tag.
 */
final class VarTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): VarTag
    {
        $type = $content->nextType($name, $this->parser);
        $variable = $content->nextOptionalVariable();

        return new VarTag(
            name: $name,
            type: $type,
            varName: $variable,
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
