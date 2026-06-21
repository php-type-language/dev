<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PropertyTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * This class is responsible for creating "`@property-write`" tags.
 *
 * See {@see PropertyWriteTag} for details about this tag.
 */
final class PropertyWriteTagFactory implements TagFactoryInterface
{
    private readonly PropertyTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new PropertyTagFactory($parser);
    }

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): PropertyWriteTag
    {
        $property = $this->factory->create($tag, $content, $descriptions);

        return new PropertyWriteTag(
            name: $property->name,
            type: $property->type,
            variable: $property->variable,
            description: $property->description,
        );
    }
}
