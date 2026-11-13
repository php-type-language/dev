<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTag;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTag;

/**
 * Reads the read type from `@return` of the `get` hook and the write type
 * from `@param` of the `set` hook, PHP 8.4 and later. The write type of a
 * property with the `set` hook is closed to the other providers.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class HookTypeProvider implements PropertyTypeProviderInterface
{
    public function readTypes(PropertyContext $property): PropertyTypes
    {
        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $get = $property->reflection->getHook(\PropertyHookType::Get);
        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $set = $property->reflection->getHook(\PropertyHookType::Set);

        return new PropertyTypes(
            readType: self::findReadType($property, $get),
            writeType: self::findWriteType($property, $set),
            isWriteTypeClosed: $set !== null,
        );
    }

    private static function findReadType(PropertyContext $property, ?\ReflectionMethod $get): ?TypeMetadata
    {
        if ($get === null) {
            return null;
        }

        $tag = $property->findTag(
            docComment: $get->getDocComment(),
            class: ReturnTag::class,
            variable: $property->reflection->name,
        );

        return $property->createType($tag);
    }

    private static function findWriteType(PropertyContext $property, ?\ReflectionMethod $set): ?TypeMetadata
    {
        if ($set === null) {
            return null;
        }

        $parameter = $set->getParameters()[0]->name ?? '';

        if ($parameter === '') {
            return null;
        }

        $tag = $property->findTag(
            docComment: $set->getDocComment(),
            class: ParamTag::class,
            variable: $parameter,
        );

        return $property->createType($tag);
    }
}
