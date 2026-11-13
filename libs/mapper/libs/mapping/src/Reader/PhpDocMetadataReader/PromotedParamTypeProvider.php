<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

/**
 * Reads the type of both directions of a promoted property from `@param`
 * of the constructor. An unparsable `@param` belongs to each promoted
 * property without a tag naming it.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PromotedParamTypeProvider implements PropertyTypeProviderInterface
{
    public function __construct(
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $paramTags,
    ) {}

    public function readTypes(PropertyContext $property): PropertyTypes
    {
        $constructor = $property->scope->getConstructor();

        if ($constructor === null || !$property->reflection->isPromoted()) {
            return new PropertyTypes();
        }

        $tag = $property->docBlocks->findVariableTag(
            docComment: $constructor->getDocComment(),
            names: $this->paramTags,
            variable: $property->reflection->name,
        );

        $type = $property->createType($tag);

        return new PropertyTypes(readType: $type, writeType: $type);
    }
}
