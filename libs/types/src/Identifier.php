<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @phpstan-consistent-constructor
 */
final class Identifier extends Node implements \Stringable
{
    /**
     * @var list<non-empty-string>
     */
    protected const array SPECIAL_CLASS_NAME = [
        'self',
        'parent',
        'static',
    ];

    /**
     * @var list<non-empty-string>
     */
    protected const array BUILTIN_TYPE_NAME = [
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
     * Returns {@see true} if the identifier contains the name of
     * a "virtual" type, i.e. invalid in the PHP namespace.
     *
     * - `SomeClass` - Non-virtual, can be a type in PHP.
     * - `false` - Non-virtual, can be a type in PHP.
     * - `non-empty-array` - Virtual, cannot be defined in PHP.
     * - `empty-string` - Virtual, cannot be defined in PHP.
     */
    public bool $isVirtual {
        get => \str_contains($this->value, '-');
    }

    /**
     * Returns {@see true} in case of name contains special class reference.
     */
    public bool $isSpecial {
        get => static::isLooksLikeSpecial($this->value);
    }

    /**
     * Returns {@see true} in case of name contains builtin type name.
     */
    public bool $isBuiltin {
        get => static::isLooksLikeBuiltin($this->value);
    }

    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $value,
    ) {}

    public static function createFromString(string|\Stringable $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        $normalized = \trim((string) $value);

        if ($normalized === '') {
            throw new \InvalidArgumentException('Name identifier cannot be empty');
        }

        return new static($normalized);
    }

    /**
     * Returns {@see true} in case of passed "$name" argument looks like
     * a special type name or {@see false} instead.
     */
    public static function isLooksLikeSpecial(string $name): bool
    {
        return \in_array(\strtolower($name), static::SPECIAL_CLASS_NAME, true);
    }

    /**
     * Returns {@see true} in case of passed "$name" argument looks like
     * a builtin type name or {@see false} instead.
     */
    public static function isLooksLikeBuiltin(string $value): bool
    {
        return \in_array(\strtolower($value), static::BUILTIN_TYPE_NAME, true);
    }

    /**
     * Returns name as string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Returns lowercased name as string.
     *
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

    /**
     * @return array{int<0, max>, non-empty-string}
     */
    public function __serialize(): array
    {
        return [$this->offset, $this->value];
    }

    /**
     * @param array{0?: int<0, max>, 1?: non-empty-string} $data
     *
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->offset = $data[0] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Identifier offset',
        );

        $this->value = $data[1] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Identifier value',
        );
    }
}
