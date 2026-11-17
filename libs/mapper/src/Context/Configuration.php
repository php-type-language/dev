<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Context;

/**
 * Settings of the conversion.
 */
final class Configuration
{
    public function __construct(
        /**
         * Whether a value must match the type exactly, without coercion
         */
        public readonly bool $isStrict = false,
        /**
         * Whether an object is normalized to an array or to a
         * {@see \stdClass}, a class may override it
         */
        public readonly bool $isObjectAsArray = true,
    ) {}

    /**
     * Returns a copy with the strictness overridden, the other settings are
     * kept.
     */
    public function withStrictTypes(bool $isStrict): self
    {
        return new self(
            isStrict: $isStrict,
            isObjectAsArray: $this->isObjectAsArray,
        );
    }
}
