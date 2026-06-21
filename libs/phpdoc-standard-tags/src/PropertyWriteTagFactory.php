<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Standard;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Tag\Content;
use TypeLang\PhpDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@property-write`" tags.
 *
 * See {@see PropertyWriteTag} for details about this tag.
 */
final class PropertyWriteTagFactory implements FactoryInterface
{
    private readonly PropertyTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new PropertyTagFactory($parser);
    }

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): PropertyWriteTag
    {
        $property = $this->factory->create($name, $content, $descriptions);

        return new PropertyWriteTag(
            name: $property->getName(),
            type: $property->getType(),
            variable: $property->getVariableName(),
            description: $property->getDescription(),
        );
    }
}
