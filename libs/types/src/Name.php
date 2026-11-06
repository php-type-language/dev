<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Name\NamespaceName;

/**
 * A name, that is, the identifiers ({@see Identifier}) it is written of and the
 * namespace separators between them.
 *
 * ```
 *  Some\Any
 *  ^^^^ ^^^ a relative name of two parts
 *
 *  \Some\Any
 *  ^         a fully qualified name begins in a separator
 * ```
 *
 * @template TNonEmpty of bool
 *
 * @template-implements \IteratorAggregate<array-key, Identifier>
 *
 * @phpstan-consistent-constructor
 */
abstract class Name extends Node implements \IteratorAggregate, \Countable, \Stringable
{
    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    /**
     * The identifiers a name is written of.
     *
     * @var (TNonEmpty is true ? non-empty-list<Identifier> : list<Identifier>)
     */
    public readonly array $parts;

    /**
     * @param iterable<array-key, Identifier> $parts
     * @param int<0, max> $offset
     */
    public function __construct(
        iterable $parts,
        /**
         * Whether a name is written with the leading separator that says it
         * is to be read from the root and not from wherever it stands.
         *
         * ```
         *  Some\Any   // false
         *  \Some\Any  // true
         * ```
         */
        public readonly bool $isFullyQualified = false,
        int $offset = 0,
    ) {
        // @phpstan-ignore-next-line : A subclass decides whether the parts may be empty
        $this->parts = self::normalizeParts($parts);

        parent::__construct($offset);
    }

    /**
     * @param iterable<array-key, Identifier> $parts
     * @return (TNonEmpty is true ? non-empty-list<Identifier> : list<Identifier>)
     */
    protected static function normalizeParts(iterable $parts): array
    {
        /** @var (TNonEmpty is true ? non-empty-list<Identifier> : list<Identifier>) */
        return match (true) {
            $parts instanceof \Traversable => \iterator_to_array($parts, false),
            \array_is_list($parts) => $parts,
            default => \array_values($parts),
        };
    }

    /**
     * Splits a name written as a string into its identifiers.
     *
     * @return list<Identifier>
     */
    protected static function parseParts(string $name): array
    {
        $parts = [];

        foreach (\explode(self::NAMESPACE_DELIMITER, $name) as $segment) {
            if ($segment === '') {
                continue;
            }

            $parts[] = Identifier::from($segment);
        }

        return $parts;
    }

    /**
     * Whether a name written as a string begins with the namespace separator.
     */
    protected static function parseFullyQualified(string $name): bool
    {
        return \str_starts_with($name, self::NAMESPACE_DELIMITER);
    }

    /**
     * Whether the name is made of a single identifier.
     */
    public function isSimple(): bool
    {
        return \count($this->parts) === 1;
    }

    /**
     * Whether the name is one of `self`, `parent` and `static`.
     */
    public function isSpecial(): bool
    {
        return $this->isSimple() && $this->parts[0]->isSpecial();
    }

    /**
     * Whether the name is the name of a builtin type.
     */
    public function isBuiltin(): bool
    {
        return $this->isSimple() && $this->parts[0]->isBuiltin();
    }

    /**
     * Gets a part of the name, which may be empty.
     *
     * @param int<0, max> $offset
     * @param int<0, max>|null $length
     */
    public function slice(int $offset = 0, ?int $length = null): NamespaceName
    {
        return new NamespaceName(
            parts: \array_slice($this->parts, $offset, $length),
            isFullyQualified: $this->isFullyQualified,
            offset: $this->offset,
        );
    }

    /**
     * Appends the passed name to the existing one at the end.
     *
     * ```
     *  Some\Any + Test\Class  // Some\Any\Test\Class
     * ```
     *
     * @param Name<bool> $name
     */
    public function withAdded(self $name): static
    {
        // @phpstan-ignore-next-line : PHPStan does not infer the template of a new static
        return new static([
            ...$this->parts,
            ...$name->parts,
        ], $this->isFullyQualified, $this->offset);
    }

    /**
     * Replaces the first part of the passed name with this one. It is the
     * way a name written through an imported alias is expanded.
     *
     * ```
     *  // use TypeLang\Parser\Exception as Error;
     *
     *  TypeLang\Parser\Exception + Error\SemanticException
     *  // TypeLang\Parser\Exception\SemanticException
     * ```
     *
     * @param Name<bool> $name
     */
    public function expandAlias(self $name): static
    {
        // @phpstan-ignore-next-line : PHPStan does not infer the template of a new static
        return new static([
            ...$this->parts,
            ...\array_slice($name->parts, 1),
        ], $this->isFullyQualified, $this->offset);
    }

    /**
     * Gets the same name with the leading separator.
     */
    public function toFullyQualified(): static
    {
        if ($this->isFullyQualified) {
            return clone $this;
        }

        // @phpstan-ignore-next-line : PHPStan does not infer the template of a new static
        return new static($this->parts, true, $this->offset);
    }

    /**
     * Gets the same name without the leading separator.
     */
    public function toUnqualified(): static
    {
        if ($this->isFullyQualified) {
            // @phpstan-ignore-next-line : PHPStan does not infer the template of a new static
            return new static($this->parts, false, $this->offset);
        }

        return clone $this;
    }

    /**
     * @return (TNonEmpty is true ? non-empty-list<Identifier> : list<Identifier>)
     */
    public function toArray(): array
    {
        return $this->parts;
    }

    /**
     * @return (TNonEmpty is true ? non-empty-list<non-empty-string> : list<non-empty-string>)
     */
    public function toArrayStrings(): array
    {
        $result = [];

        foreach ($this->parts as $identifier) {
            $result[] = $identifier->toString();
        }

        /** @var (TNonEmpty is true ? non-empty-list<non-empty-string> : list<non-empty-string>) */
        return $result;
    }

    /**
     * @return (TNonEmpty is true ? non-empty-list<non-empty-lowercase-string> : list<non-empty-lowercase-string>)
     */
    public function toArrayLowercaseStrings(): array
    {
        $result = [];

        foreach ($this->parts as $identifier) {
            $result[] = $identifier->toLowerString();
        }

        /** @var (TNonEmpty is true ? non-empty-list<non-empty-lowercase-string> : list<non-empty-lowercase-string>) */
        return $result;
    }

    /**
     * Returns a name as it is written, with the leading separator if it has one.
     *
     * @return (TNonEmpty is true ? non-empty-string : string)
     */
    public function toString(): string
    {
        if ($this->isFullyQualified) {
            return $this->toFullyQualifiedString();
        }

        return $this->toUnqualifiedString();
    }

    /**
     * Returns a name without the leading separator.
     *
     * @return (TNonEmpty is true ? non-empty-string : string)
     */
    public function toUnqualifiedString(): string
    {
        return \implode(self::NAMESPACE_DELIMITER, $this->toArrayStrings());
    }

    /**
     * Returns a name with the leading separator.
     *
     * @return non-empty-string
     */
    public function toFullyQualifiedString(): string
    {
        return self::NAMESPACE_DELIMITER . $this->toUnqualifiedString();
    }

    /**
     * @return (TNonEmpty is true ? non-empty-lowercase-string : lowercase-string)
     */
    public function toLowerString(): string
    {
        return \strtolower($this->toString());
    }

    /**
     * @return (TNonEmpty is true ? non-empty-lowercase-string : lowercase-string)
     */
    public function toUnqualifiedLowerString(): string
    {
        return \strtolower($this->toUnqualifiedString());
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function toFullyQualifiedLowerString(): string
    {
        return \strtolower($this->toFullyQualifiedString());
    }

    /**
     * @return \Traversable<array-key, Identifier>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->parts);
    }

    /**
     * @return (TNonEmpty is true ? int<1, max> : int<0, max>)
     */
    public function count(): int
    {
        return \count($this->parts);
    }

    /**
     * @return (TNonEmpty is true ? non-empty-string : string)
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
