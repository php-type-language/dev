<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Standard;

use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@property-read`" tags.
 *
 * See {@see PropertyReadTag} for details about this tag.
 */
final class PropertyReadTagFactory implements FactoryInterface
{
    private readonly PropertyTagFactory $factory;

    public function __construct(
        TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {
        $this->factory = new PropertyTagFactory($parser);
    }

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): PropertyReadTag
    {
        $property = $this->factory->create($name, $content, $descriptions);

        return new PropertyReadTag(
            name: $property->getName(),
            type: $property->getType(),
            variable: $property->getVariableName(),
            description: $property->getDescription(),
        );
    }
}
