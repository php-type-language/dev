<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

/**
 * Reads the type of both directions from `@var` of the property.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class VarTagTypeProvider implements PropertyTypeProviderInterface
{
    public function __construct(
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $varTags,
    ) {}

    public function readTypes(PropertyContext $property): PropertyTypes
    {
        $tag = $property->docBlocks->findTag(
            docComment: $property->reflection->getDocComment(),
            names: $this->varTags,
            variable: $property->reflection->name,
        );

        $type = $property->createType($tag);

        return new PropertyTypes(readType: $type, writeType: $type);
    }
}
