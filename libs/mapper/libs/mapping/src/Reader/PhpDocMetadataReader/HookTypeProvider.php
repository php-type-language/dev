<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;

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
    public function __construct(
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $paramTags,
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $returnTags,
    ) {}

    public function readTypes(PropertyContext $property): PropertyTypes
    {
        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $get = $property->reflection->getHook(\PropertyHookType::Get);
        /** @var \ReflectionMethod|null */
        // @phpstan-ignore-next-line PHP 8.4 API is unknown for PHP 8.1
        $set = $property->reflection->getHook(\PropertyHookType::Set);

        return new PropertyTypes(
            readType: $this->findReadType($property, $get),
            writeType: $this->findWriteType($property, $set),
            isWriteTypeClosed: $set !== null,
        );
    }

    private function findReadType(PropertyContext $property, ?\ReflectionMethod $get): ?TypeMetadata
    {
        if ($get === null) {
            return null;
        }

        $tag = $property->docBlocks->findTag(
            docComment: $get->getDocComment(),
            names: $this->returnTags,
            variable: $property->reflection->name,
        );

        return $property->createType($tag);
    }

    private function findWriteType(PropertyContext $property, ?\ReflectionMethod $set): ?TypeMetadata
    {
        if ($set === null) {
            return null;
        }

        $parameter = $set->getParameters()[0]->name ?? '';

        if ($parameter === '') {
            return null;
        }

        $tag = $property->docBlocks->findVariableTag(
            docComment: $set->getDocComment(),
            names: $this->paramTags,
            variable: $parameter,
        );

        return $property->createType($tag);
    }
}
