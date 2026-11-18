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
         * Whether a value must match the type exactly on denormalization,
         * without coercion. A property may override it.
         */
        public readonly bool $strict = false,
        /**
         * Whether an object is normalized to an array or to a
         * {@see \stdClass}. A class may override it.
         */
        public readonly bool $normalizeAsArray = true,
        /**
         * Number of violations a denormalization collects before it fails,
         * the first one fails it by default
         *
         * @var int<1, max>
         */
        public readonly int $maxViolations = 1,
        /**
         * Whether a field of the data that no property of a class and no
         * field of a sealed shape takes is a violation on denormalization,
         * such fields are dropped by default
         */
        public readonly bool $rejectExtraFields = false,
        /**
         * Number of levels of the classes described by their fields in the
         * expected types of the violations, `0` describes a class as
         * `object`
         *
         * @var int<0, max>
         */
        public readonly int $expectedTypeDepth = 1,
        /**
         * Number of fields a type is described by in the expected types of
         * the violations, the others are shown as `...`, {@see null} shows
         * all of them
         *
         * @var int<1, max>|null
         */
        public readonly ?int $expectedTypeMaxFields = null,
    ) {}

    /**
     * Returns a copy with the strictness overridden, the other settings are
     * kept.
     */
    public function withStrict(bool $strict): self
    {
        return new self(
            strict: $strict,
            normalizeAsArray: $this->normalizeAsArray,
            maxViolations: $this->maxViolations,
            rejectExtraFields: $this->rejectExtraFields,
            expectedTypeDepth: $this->expectedTypeDepth,
            expectedTypeMaxFields: $this->expectedTypeMaxFields,
        );
    }
}
