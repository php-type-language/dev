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
    protected const SPECIAL_CLASS_NAME = [
        'self',
        'parent',
        'static',
    ];

    /**
     * @var list<non-empty-string>
     */
    protected const BUILTIN_TYPE_NAME = [
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
    public static function createFromString(string|\Stringable $value, int $offset = 0): self
    {
        if ($value instanceof self) {
            return $value;
        }

        $normalized = \trim((string) $value);

        if ($normalized === '') {
            throw new \InvalidArgumentException('Name identifier cannot be empty');
        }

        return new self($normalized, $offset);
    }

    /**
     * Returns {@see true} in case of passed "$name" argument looks like
     * a special type name or {@see false} instead.
     */
    public static function isLooksLikeSpecial(string $name): bool
    {
        return \in_array(\strtolower($name), self::SPECIAL_CLASS_NAME, true);
    }

    /**
     * Returns {@see true} in case of passed "$name" argument looks like
     * a builtin type name or {@see false} instead.
     */
    public static function isLooksLikeBuiltin(string $value): bool
    {
        return \in_array(\strtolower($value), self::BUILTIN_TYPE_NAME, true);
    }

    /**
     * Returns {@see true} if the identifier contains the name of a "virtual"
     * type, i.e. invalid in the PHP namespace.
     *
     * - `SomeClass` - Non-virtual, can be a type in PHP.
     * - `false` - Non-virtual, can be a type in PHP.
     * - `non-empty-array` - Virtual, cannot be defined in PHP.
     * - `empty-string` - Virtual, cannot be defined in PHP.
     */
    public function isVirtual(): bool
    {
        return \str_contains($this->value, '-');
    }

    /**
     * Returns {@see true} in case of name contains special class reference.
     */
    public function isSpecial(): bool
    {
        return self::isLooksLikeSpecial($this->value);
    }

    /**
     * Returns {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        return self::isLooksLikeBuiltin($this->value);
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
