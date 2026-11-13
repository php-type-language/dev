<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTag;

/**
 * Reads the type of both directions of a promoted property from `@param`
 * of the constructor naming it.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PromotedParamTypeProvider implements PropertyTypeProviderInterface
{
    public function readTypes(PropertyContext $property): PropertyTypes
    {
        $constructor = $property->scope->getConstructor();

        if ($constructor === null || !$property->reflection->isPromoted()) {
            return new PropertyTypes();
        }

        $tag = $property->findTag(
            docComment: $constructor->getDocComment(),
            class: ParamTag::class,
            variable: $property->reflection->name,
        );

        $type = $property->createType($tag);

        return new PropertyTypes(
            readType: $type,
            writeType: $type,
        );
    }
}
