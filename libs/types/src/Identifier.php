<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A single word a name is made of.
 *
 * ```
 *  Some\Any
 *  ^^^^ ^^^ // two identifiers
 *
 *  non-empty-string
 *  ^^^^^^^^^^^^^^^^ // a single identifier
 * ```
 */
final class Identifier extends Node implements \Stringable
{
    /**
     * @var list<non-empty-string>
     */
    private const SPECIAL_CLASS_NAME = [
        'self',
        'parent',
        'static',
    ];

    /**
     * @var list<non-empty-string>
     */
    private const BUILTIN_TYPE_NAME = [
        'mixed',
        'string',
        'int',
        'float',
        'bool',
        'object',
        'array',
        'void',
        'never',
        'callable',
        'iterable',
        'null',
        'true',
        'false',
    ];

    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of an empty value
     */
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $value,
        int $offset = 0,
    ) {
        if ($value === '') {
            throw new \InvalidArgumentException('Name identifier cannot be empty');
        }

        parent::__construct($offset);
    }

    /**
     * @param int<0, max> $offset
     */
    public static function from(string|\Stringable $value, int $offset = 0): self
    {
        if ($value instanceof self && $offset === $value->offset) {
            return $value;
        }

        $normalized = \trim((string) $value);

        if ($normalized === '') {
            throw new \InvalidArgumentException('Name identifier cannot be empty');
        }

        return new self($normalized, $offset);
    }

    /**
     * Whether a string, in any case, is a special class name: `self`,
     * `parent` or `static`.
     */
    public static function isSpecialClassName(string $name): bool
    {
        return \in_array(\strtolower($name), self::SPECIAL_CLASS_NAME, true);
    }

    /**
     * Whether a string, in any case, is the name of a builtin type, like
     * `int` or `iterable`.
     */
    public static function isBuiltinTypeName(string $value): bool
    {
        return \in_array(\strtolower($value), self::BUILTIN_TYPE_NAME, true);
    }

    /**
     * Whether the identifier is the name of a "virtual" type, which cannot
     * be defined in PHP.
     *
     * - `SomeClass`, `false` - not virtual.
     * - `non-empty-array`, `empty-string` - virtual.
     */
    public function isVirtual(): bool
    {
        return \str_contains($this->value, '-');
    }

    public function isSpecial(): bool
    {
        return self::isSpecialClassName($this->value);
    }

    public function isBuiltin(): bool
    {
        return self::isBuiltinTypeName($this->value);
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function toLowerString(): string
    {
        return \strtolower($this->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
