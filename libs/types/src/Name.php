<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A name: the words it is written of, the separators between them and
 * the one it may begin with.
 *
 * ```
 *  Some\Any
 *  ^^^^^^^^ a relative name
 *
 *  \Some\Any
 *  ^^^^^^^^^ a fully qualified name begins in a separator
 *
 *  non-empty-string
 *  ^^^^^^^^^^^^^^^^ a single word
 * ```
 *
 * @template-implements \IteratorAggregate<int<0, max>, non-empty-string>
 * @template-implements \ArrayAccess<int<0, max>, non-empty-string>
 */
final class Name extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Stringable,
    \Countable
{
    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    /**
     * @var non-empty-array<non-empty-string, true>
     */
    private const SPECIAL_CLASS_NAME = [
        'self' => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * @var non-empty-string
     */
    public readonly string $unqualified;

    /**
     * @var non-empty-list<non-empty-string>
     *
     * @phpstan-ignore-next-line : PHPStan does not support lazy init
     */
    private readonly array $parts;

    /**
     * Whether a name is written with the leading separator that says it
     * is to be read from the root and not from wherever it stands.
     *
     * ```
     *  Some\Any   // false
     *  \Some\Any  // true
     * ```
     */
    public readonly bool $isFullyQualified;

    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the value has nothing but separators
     */
    public function __construct(string $value, int $offset = 0)
    {
        $unqualified = \trim($value, self::NAMESPACE_DELIMITER);

        if ($unqualified === '') {
            throw new \InvalidArgumentException('Name cannot be empty');
        }

        $this->isFullyQualified = $value[0] === self::NAMESPACE_DELIMITER;
        $this->unqualified = $unqualified;

        parent::__construct($offset);
    }

    /**
     * Whether the name is made of a single identifier.
     *
     * @api
     */
    public function isSimple(): bool
    {
        return !\str_contains($this->unqualified, self::NAMESPACE_DELIMITER);
    }

    /**
     * Whether the name is the name of a "virtual" type, which cannot be
     * defined in PHP.
     *
     * - `SomeClass`, `false` - not virtual.
     * - `non-empty-array`, `empty-string` - virtual.
     *
     * @api
     */
    public function isVirtual(): bool
    {
        return \str_contains($this->unqualified, '-');
    }

    /**
     * Whether the name is one of `self`, `parent` and `static`.
     *
     * @api
     */
    public function isSpecial(): bool
    {
        return $this->isFullyQualified === false
            && isset(self::SPECIAL_CLASS_NAME[\strtolower($this->unqualified)]);
    }

    /**
     * Gets the segments of the name without the separators.
     *
     * ```
     *  \Some\Any  // ['Some', 'Any']
     * ```
     *
     * @api
     *
     * @return non-empty-list<non-empty-string>
     */
    public function toArray(): array
    {
        if (isset($this->parts)) {
            return $this->parts;
        }

        $parts = [];

        foreach (\explode(self::NAMESPACE_DELIMITER, $this->unqualified) as $segment) {
            if ($segment !== '') {
                $parts[] = $segment;
            }
        }

        /** @phpstan-ignore-next-line : PHPStan does not support lazy initialization */
        return $this->parts = $parts;
    }

    /**
     * Gets the first segment of the name.
     *
     * ```
     *  Some\Any  // Some
     * ```
     *
     * @api
     *
     * @return non-empty-string
     */
    public function first(): string
    {
        $first = \strpos($this->unqualified, self::NAMESPACE_DELIMITER);

        if ($first !== false) {
            /** @var non-empty-string */
            return \substr($this->unqualified, 0, $first);
        }

        return $this->unqualified;
    }

    /**
     * Gets the last segment of the name.
     *
     * ```
     *  Some\Any  // Any
     * ```
     *
     * @api
     *
     * @return non-empty-string
     */
    public function last(): string
    {
        $last = \strrpos($this->unqualified, self::NAMESPACE_DELIMITER);

        if ($last !== false) {
            /** @var non-empty-string */
            return \substr($this->unqualified, $last + 1);
        }

        return $this->unqualified;
    }

    /**
     * Gets the same name with the leading separator.
     *
     * @api
     */
    public function toFullyQualified(): self
    {
        if ($this->isFullyQualified) {
            return $this;
        }

        return new self(self::NAMESPACE_DELIMITER . $this->unqualified, $this->offset);
    }

    /**
     * Gets the same name without the leading separator.
     *
     * @api
     */
    public function toUnqualified(): self
    {
        if (!$this->isFullyQualified) {
            return $this;
        }

        return new self($this->unqualified, $this->offset);
    }

    /**
     * Appends the segments of the passed name to the end of this one. The
     * leading separator of the passed name is dropped, the one of this name
     * is kept.
     *
     * ```
     *  Some\Any + Test\Class   // Some\Any\Test\Class
     *  \Some\Any + \Test       // \Some\Any\Test
     * ```
     *
     * @api
     *
     * @throws \InvalidArgumentException in case of the passed name is empty
     */
    public function withAdded(string|\Stringable $name): self
    {
        $name = self::fromNameLike($name);

        $result = $this->toString() . self::NAMESPACE_DELIMITER . $name->unqualified;

        return new self($result, $this->offset);
    }

    /**
     * Replaces the last segment of the name with the passed name. The leading
     * separator of the passed name is dropped, the one of this name is kept.
     *
     * ```
     *  Some\Any -> Other   // Some\Other
     *  \Some    -> Other   // \Other
     * ```
     *
     * @api
     *
     * @throws \InvalidArgumentException in case of the passed name is empty
     */
    public function withReplaced(string|\Stringable $name): self
    {
        $name = self::fromNameLike($name);

        $prefix = '';

        if ($this->isFullyQualified) {
            $prefix = self::NAMESPACE_DELIMITER;
        }

        $position = \strrpos($this->unqualified, self::NAMESPACE_DELIMITER);

        if ($position !== false) {
            $prefix .= \substr($this->unqualified, 0, $position + 1);
        }

        return new self($prefix . $name->unqualified, $this->offset);
    }

    /**
     * @throws \InvalidArgumentException in case of the name is empty
     */
    private static function fromNameLike(string|\Stringable $name): self
    {
        if ($name instanceof self) {
            return $name;
        }

        /** @var non-empty-string $value The constructor rejects an empty one */
        $value = (string) $name;

        return new self($value);
    }

    /**
     * Returns the name as it is written, with the leading separator if it has
     * one.
     *
     * @api
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        if ($this->isFullyQualified) {
            return self::NAMESPACE_DELIMITER . $this->unqualified;
        }

        return $this->unqualified;
    }

    /**
     * Returns the name as it is written, in lower case.
     *
     * @api
     *
     * @return non-empty-lowercase-string
     */
    public function toLowerString(): string
    {
        return \strtolower($this->toString());
    }

    /**
     * Iterates the segments of the name.
     *
     * @return \Traversable<int<0, max>, non-empty-string>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Gets the count of the segments of the name.
     *
     * @return int<1, max>
     */
    public function count(): int
    {
        return \count($this->toArray());
    }

    public function offsetExists(mixed $offset): bool
    {
        assert(\is_int($offset) && $offset >= 0, new \InvalidArgumentException('Invalid name segment offset'));

        $parts = $this->toArray();

        return isset($parts[$offset]);
    }

    public function offsetGet(mixed $offset): string
    {
        assert(\is_int($offset) && $offset >= 0, new \InvalidArgumentException('Invalid name segment offset'));

        $parts = $this->toArray();

        return $parts[$offset] ?? throw new \OutOfBoundsException('Invalid name segment offset');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        assert(\is_int($offset) && $offset >= 0, new \InvalidArgumentException('Invalid name segment offset'));

        throw new \LogicException('Cannot modify name parts');
    }

    public function offsetUnset(mixed $offset): void
    {
        assert(\is_int($offset) && $offset >= 0, new \InvalidArgumentException('Invalid name segment offset'));

        throw new \LogicException('Cannot modify name parts');
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
