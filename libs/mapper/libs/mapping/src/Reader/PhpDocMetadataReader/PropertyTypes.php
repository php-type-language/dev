<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;

/**
 * Docblock types of a property found by a provider, an unset type is taken
 * from the providers of lower priority.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PropertyTypes
{
    public function __construct(
        public readonly ?TypeMetadata $readType = null,
        public readonly ?TypeMetadata $writeType = null,
        /**
         * Whether the write type is closed to the providers of lower
         * priority, even when it is not set
         */
        public readonly bool $isWriteTypeClosed = false,
    ) {}

    /**
     * Returns {@see true} in case of the providers of lower priority cannot
     * set any type.
     */
    public function isComplete(): bool
    {
        return $this->readType !== null
            && ($this->writeType !== null || $this->isWriteTypeClosed);
    }

    /**
     * Returns these types with the unset ones taken from the types of lower
     * priority.
     */
    public function withLower(self $lower): self
    {
        $writeType = $this->writeType;

        if (!$this->isWriteTypeClosed) {
            $writeType ??= $lower->writeType;
        }

        return new self(
            readType: $this->readType ?? $lower->readType,
            writeType: $writeType,
            isWriteTypeClosed: $this->isWriteTypeClosed || $lower->isWriteTypeClosed,
        );
    }
}
