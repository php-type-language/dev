<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TypedTag;

/**
 * Property whose docblock types are read: its declaration, the docblocks
 * of the current reading and the resolution of the found types.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PropertyContext
{
    public function __construct(
        public readonly \ReflectionProperty $reflection,
        /**
         * Class or trait whose body declares the property
         *
         * @var \ReflectionClass<object>
         */
        public readonly \ReflectionClass $scope,
        public readonly DocBlockReader $docBlocks,
        private readonly SourceContext $sourceContext,
    ) {}

    /**
     * @param \ReflectionClass<object> $class
     */
    public static function create(
        \ReflectionClass $class,
        \ReflectionProperty $reflection,
        DocBlockReader $docBlocks,
        SourceContextFactory $contexts,
    ): self {
        return new self(
            reflection: $reflection,
            scope: $contexts->findScope($reflection),
            docBlocks: $docBlocks,
            sourceContext: $contexts->createFromReflection($class, $reflection),
        );
    }

    /**
     * Returns the type of the tag resolved by the imports of the
     * declaration, a type with an error for the reason the tag cannot be
     * read, or {@see null} in case of there is no tag.
     */
    public function createType(TypedTag|\Throwable|null $tag): ?TypeMetadata
    {
        if ($tag === null) {
            return null;
        }

        if ($tag instanceof \Throwable) {
            return $this->sourceContext->createInvalidType($tag);
        }

        return $this->sourceContext->createType($this->sourceContext->resolve(clone $tag->type));
    }
}
