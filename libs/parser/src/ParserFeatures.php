<?php

declare(strict_types=1);

namespace TypeLang\Parser;

final readonly class ParserFeatures
{
    public const bool CONDITIONAL_FEATURES_DEFAULT_VALUE = true;
    public const bool SHAPES_FEATURES_DEFAULT_VALUE = true;
    public const bool CALLABLES_FEATURES_DEFAULT_VALUE = true;
    public const bool LITERALS_FEATURES_DEFAULT_VALUE = true;
    public const bool GENERICS_FEATURES_DEFAULT_VALUE = true;
    public const bool UNION_FEATURES_DEFAULT_VALUE = true;
    public const bool INTERSECTION_FEATURES_DEFAULT_VALUE = true;
    public const bool LIST_FEATURES_DEFAULT_VALUE = true;
    public const bool OFFSETS_FEATURES_DEFAULT_VALUE = true;
    public const bool HINTS_FEATURES_DEFAULT_VALUE = true;
    public const bool ATTRIBUTES_FEATURES_DEFAULT_VALUE = true;

    public function __construct(
        /**
         * Enables or disables support for dependent/conditional
         * types such as `T ? U : V`
         */
        public bool $conditions = self::CONDITIONAL_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for type shapes such
         * as `T{key: U}`
         */
        public bool $shapes = self::SHAPES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for callable types
         * such as `(T, U): V`
         */
        public bool $callables = self::CALLABLES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for literal types such
         * as `42` or `"string"`
         */
        public bool $literals = self::LITERALS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for template arguments
         * such as `T<U, V>`
         */
        public bool $generics = self::GENERICS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for logical union types
         * such as `T | U`
         */
        public bool $unions = self::UNION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for logical intersection
         * types such as `T & U`
         */
        public bool $intersections = self::INTERSECTION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for square bracket list
         * types such as `T[]`
         */
        public bool $lists = self::LIST_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for square bracket offset
         * access types such as `T[U]`
         */
        public bool $offsets = self::OFFSETS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for template argument hints
         * such as `T<out U, in V>`
         */
        public bool $hints = self::HINTS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for attributes such as `#[attr]`
         */
        public bool $attributes = self::ATTRIBUTES_FEATURES_DEFAULT_VALUE,
    ) {}

    /**
     * A general method with the ability to override a specific feature flag
     *
     * ```
     * $features = $features->with(
     *     conditions: true,
     *     hints: false,
     * );
     * ```
     */
    public function with(bool ...$features): self
    {
        /** @var array<non-empty-string, bool> $arguments */
        $arguments = [...\get_object_vars($this), ...$features];

        return new self(...$arguments);
    }
}
