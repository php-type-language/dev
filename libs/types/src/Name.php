<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\NamespaceName\NamespaceKind;

/**
 * A name: the namespace it begins with and the word it ends in.
 *
 * ```
 *  Some\Any
 *  ^^^^     the namespace
 *       ^^^ the word
 *
 *  \Some\Any
 *  ^^^^^     a fully qualified namespace begins in a separator
 *
 *  \Any
 *  ^    the root namespace
 *
 *  non-empty-string
 *  ^^^^^^^^^^^^^^^^ a single word, there is no namespace
 * ```
 *
 * @template-implements \IteratorAggregate<int<0, max>, non-empty-string>
 * @template-implements \ArrayAccess<int<0, max>, non-empty-string>
 */
final class Name extends SymbolNode implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @var non-empty-string
     */
    private const DELIMITER = NamespaceName::DELIMITER;

    /**
     * Words of a class that are not names of classes but stand for one.
     *
     * @var non-empty-array<non-empty-lowercase-string, true>
     */
    private const SPECIAL_CLASS_NAMES = [
        'self' => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * Words that PHP reserves for the types it has built in, so no class can
     * be named by one of them.
     *
     * @var non-empty-array<non-empty-lowercase-string, true>
     */
    private const BUILTIN_TYPE_NAMES = [
        'mixed' => true,
        'string' => true,
        'int' => true,
        'float' => true,
        'bool' => true,
        'object' => true,
        'array' => true,
        'void' => true,
        'never' => true,
        'callable' => true,
        'iterable' => true,
        'null' => true,
        'true' => true,
        'false' => true,
    ];

    /**
     * @var non-empty-list<non-empty-string>
     *
     * @phpstan-ignore-next-line : PHPStan does not support lazy init
     */
    private readonly array $parts;

    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the value is empty or has
     *         a separator
     */
    public function __construct(
        /**
         * The last word of the name, it has no separators.
         *
         * @var non-empty-string
         */
        public readonly string $value,
        /**
         * The namespace the word belongs to, or {@see null} in case of the
         * name is written with none.
         */
        public readonly ?NamespaceName $namespace = null,
        int $offset = 0,
    ) {
        if ($value === '' || \str_contains($value, NamespaceName::DELIMITER)) {
            throw new \InvalidArgumentException('Name must be a non-empty word with no separators');
        }

        parent::__construct($offset);
    }

    /**
     * Whether the text is one of `self`, `parent` and `static`.
     *
     * @api
     */
    public function isSpecial(): bool
    {
        return isset(self::SPECIAL_CLASS_NAMES[$this->toLowerString()]);
    }

    /**
     * Whether the text is the name of a type that PHP has built in, such as
     * `int`, `array` or `callable`.
     *
     * @api
     */
    public function isBuiltin(): bool
    {
        return isset(self::BUILTIN_TYPE_NAMES[$this->toLowerString()]);
    }

    /**
     * Whether a name is written with the leading separator that says it
     * is to be read from the root and not from wherever it stands.
     *
     * ```
     *  Some\Any       // false
     *  \Some\Any      // true
     *  \Any           // true
     *  namespace\Any  // false
     * ```
     *
     * @api
     */
    public function isFullyQualified(): bool
    {
        return $this->namespace?->kind === NamespaceKind::FullyQualified;
    }

    /**
     * Whether the name is made of a single word, the leading separator and
     * the `namespace` keyword are not counted.
     *
     * @api
     */
    public function isSimple(): bool
    {
        return ($this->namespace->value ?? '') === '';
    }

    /**
     * Returns the name that is made of the last word alone, with no namespace.
     *
     * ```
     *  \Some\Any  // Any
     *  Some\Any   // Any
     *  Any        // Any
     * ```
     *
     * @api
     */
    public function toUnqualified(): self
    {
        if ($this->namespace === null) {
            return $this;
        }

        return new self($this->value, null, $this->offset);
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

        $parts = $this->namespace?->toArray() ?? [];
        $parts[] = $this->value;

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
        return $this->toArray()[0];
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
        return $this->value;
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
        if ($this->namespace === null) {
            return $this->value;
        }

        if ($this->namespace->isRoot()) {
            return self::DELIMITER . $this->value;
        }

        $namespace = $this->namespace->toString();

        if ($namespace === '') {
            return $this->value;
        }

        return $namespace . self::DELIMITER . $this->value;
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
}
