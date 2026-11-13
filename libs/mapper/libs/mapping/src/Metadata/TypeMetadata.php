<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Metadata;

use TypeLang\Type\TypeNode;

/**
 * Type of a property with its options and the source that defined it: a
 * type tree or the reason the source cannot read the type.
 */
final class TypeMetadata
{
    private function __construct(
        /**
         * Type with imported names resolved, read-only by contract,
         * {@see null} in case of the type cannot be read
         */
        public readonly ?TypeNode $type,
        /**
         * Reason the type cannot be read, {@see null} in case of the type
         * is read
         *
         * @var non-empty-string|null
         */
        public readonly ?string $error,
        public readonly SourceInfo $source,
        /**
         * Namespace the type is written in, simple names of classes belong
         * to it, {@see null} in case of the global namespace or a type with
         * fully qualified names only
         *
         * @var non-empty-string|null
         */
        public readonly ?string $namespace = null,
        /**
         * Whether a value must match the type exactly, without coercion,
         * {@see null} means the mapper configuration
         */
        public readonly ?bool $isStrict = null,
    ) {}

    /**
     * @param non-empty-string|null $namespace
     */
    public static function createFromType(TypeNode $type, SourceInfo $source, ?string $namespace = null): self
    {
        return new self(
            type: $type,
            error: null,
            source: $source,
            namespace: $namespace,
        );
    }

    /**
     * @param non-empty-string $error reason the type cannot be read
     */
    public static function createFromError(string $error, SourceInfo $source): self
    {
        return new self(
            type: null,
            error: $error,
            source: $source,
        );
    }

    public function withStrict(bool $isStrict): self
    {
        return new self(
            type: $this->type,
            error: $this->error,
            source: $this->source,
            namespace: $this->namespace,
            isStrict: $isStrict,
        );
    }
}
