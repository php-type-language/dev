<?php

declare(strict_types=1);

namespace TypeLang\Type;

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
 * @phpstan-consistent-constructor
 *
 * @template-implements \IteratorAggregate<array-key, Identifier>
 */
final class Name extends Node implements \IteratorAggregate, \Countable, \Stringable
{
    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    public const IS_FULLY_QUALIFIED_DEFAULT_VALUE = false;

    /**
     * The identifiers a name is written of.
     *
     * @var non-empty-list<Identifier>
     */
    public readonly array $parts;

    /**
     * The first of the {@see $parts}, which is the one an alias is resolved by.
     */
    public readonly Identifier $first;

    /**
     * The last of the {@see $parts}, which is the short name of a class.
     */
    public readonly Identifier $last;

    /**
     * @param iterable<array-key, Identifier> $parts
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of parts are empty or contain
     *         something else than an {@see Identifier}
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
        public readonly bool $isFullyQualified = self::IS_FULLY_QUALIFIED_DEFAULT_VALUE,
        int $offset = 0,
    ) {
        $this->parts = self::formatNameParts($parts);

        $this->first = $this->parts[0];
        $this->last = $this->parts[\count($this->parts) - 1];

        parent::__construct($offset);
    }

    /**
     * @param iterable<mixed, Identifier> $parts
     * @return non-empty-list<Identifier>
     */
    private static function formatNameParts(iterable $parts): array
    {
        $parts = match (true) {
            $parts instanceof \Traversable => \iterator_to_array($parts, false),
            \array_is_list($parts) => $parts,
            default => \array_values($parts),
        };

        if ($parts === []) {
            throw new \InvalidArgumentException('Name parts count can not be empty');
        }

        return $parts;
    }

    /**
     * @param int<0, max> $offset
     */
    public static function createFromString(string|\Stringable $name, int $offset = 0): self
    {
        $name = (string) $name;
        $parts = [];

        foreach (\explode(self::NAMESPACE_DELIMITER, $name) as $segment) {
            if ($segment === '') {
                continue;
            }

            $parts[] = Identifier::createFromString($segment);
        }

        return new self($parts, \str_starts_with($name, self::NAMESPACE_DELIMITER), $offset);
    }

    /**
     * Gets the first segment of a name
     */
    public function getFirstPart(): Identifier
    {
        return $this->first;
    }

    /**
     * Gets the first segment of a name as a string
     *
     * @return non-empty-string
     */
    public function getFirstPartAsString(): string
    {
        return $this->first->toString();
    }

    /**
     * Gets the first segment of a name as a lowercase string
     *
     * @return non-empty-lowercase-string
     */
    public function getFirstPartAsLowerString(): string
    {
        return $this->first->toLowerString();
    }

    /**
     * Gets the last segment of a name
     */
    public function getLastPart(): Identifier
    {
        return $this->last;
    }

    /**
     * Gets the last segment of a name as a string
     *
     * @return non-empty-string
     */
    public function getLastPartAsString(): string
    {
        return $this->last->toString();
    }

    /**
     * Gets the last segment of a name as a lowercase string
     *
     * @return non-empty-lowercase-string
     */
    public function getLastPartAsLowerString(): string
    {
        return $this->last->toLowerString();
    }

    /**
     * Gets whether the name is simple.
     */
    public function isSimple(): bool
    {
        return \count($this->parts) === 1;
    }

    /**
     * Gets {@see true} in case of name contains special class reference.
     */
    public function isSpecial(): bool
    {
        return $this->isSimple() && $this->getFirstPart()->isSpecial();
    }

    /**
     * Gets {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        return $this->isSimple() && $this->getFirstPart()->isBuiltin();
    }

    /**
     * Gets {@see true} in case of name is fully qualified.
     *
     * @deprecated use the {@see $isFullyQualified} property instead
     */
    public function isFullQualified(): bool
    {
        return $this->isFullyQualified;
    }

    /**
     * @param int<0, max> $offset
     * @param int<0, max>|null $length
     */
    public function slice(int $offset = 0, ?int $length = null): self
    {
        return new self(
            parts: \array_slice($this->parts, $offset, $length),
            isFullyQualified: $this->isFullyQualified,
        );
    }

    /**
     * Appends the passed {@see Name} to the existing one at the end.
     *
     * ```php
     *  $name = new Name('Some\Any');
     *
     *  echo $name->withAdded(new Name('Test\Class'));
     *  > "Some\Any\Test\Class"
     *
     *  echo $name->withAdded(new Name('Any\Class'));
     *  > "Some\Any\Any\Class"
     * ```
     */
    public function withAdded(self $name): self
    {
        return new self([
            ...$this->parts,
            ...$name->parts,
        ], $this->isFullyQualified);
    }

    /**
     * Combines two names into one (in case the last one is an alias).
     *
     * ```php
     *   $name = new Name('Some\Any');
     *
     *   echo $name->mergeWith(new Name('Test\Class'));
     *   > "Some\Any\Class"
     *
     *   echo $name->mergeWith(new Name('Any\Class'));
     *   > "Some\Any\Class"
     * ```
     *
     * Real world use case:
     * ```php
     *  // use TypeLang\Parser\Node;
     *  // echo Node::class;
     *
     *  $name = new Name('TypeLang\Parser\Node');
     *  echo $name->mergeWith(new Name('Node'));
     *
     *  // > TypeLang\Parser\Node
     * ```
     *
     * Or aliased:
     * ```php
     *  // use TypeLang\Parser\Exception as Error;
     *  // echo Error\SemanticException::class;
     *
     *  $name = new Name('TypeLang\Parser\Exception');
     *  echo $name->mergeWith(new Name('Error\SemanticException'));
     *
     *  // > TypeLang\Parser\Exception\SemanticException
     * ```
     */
    public function mergeWith(self $name): self
    {
        return new self([
            ...$this->parts,
            ...\array_slice($name->parts, 1),
        ], $this->isFullyQualified);
    }

    /**
     * Convert a name to a full qualified name instance.
     */
    public function toFullQualified(): self
    {
        if ($this->isFullyQualified) {
            return clone $this;
        }

        return new self($this->parts, true);
    }

    /**
     * Convert name to unqualified name instance.
     */
    public function toUnqualified(): self
    {
        if ($this->isFullyQualified) {
            return new self($this->parts, false);
        }

        return clone $this;
    }

    /**
     * @return non-empty-list<Identifier>
     */
    public function toArray(): array
    {
        return $this->parts;
    }

    /**
     * @deprecated use the {@see $parts} property instead
     *
     * @return non-empty-list<Identifier>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function toArrayStrings(): array
    {
        $result = [];

        foreach ($this->parts as $identifier) {
            $result[] = $identifier->toString();
        }

        return $result;
    }

    /**
     * @deprecated use the {@see toArrayStrings()} method instead
     *
     * @return non-empty-list<non-empty-string>
     */
    public function getPartsAsString(): array
    {
        return $this->toArrayStrings();
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function toArrayLowercaseStrings(): array
    {
        $result = [];

        foreach ($this->parts as $identifier) {
            $result[] = $identifier->toLowerString();
        }

        return $result;
    }

    /**
     * Returns a name as a string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        if ($this->isFullyQualified) {
            return $this->toFullQualifiedString();
        }

        return $this->toUnqualifiedString();
    }

    /**
     * Returns a name as an unqualified (without the initial `\\`) string.
     *
     * @return non-empty-string
     */
    public function toUnqualifiedString(): string
    {
        return \implode(self::NAMESPACE_DELIMITER, $this->toArrayStrings());
    }

    /**
     * Returns a name as full qualified (with the initial `\\`) string.
     *
     * @return non-empty-string
     */
    public function toFullQualifiedString(): string
    {
        return self::NAMESPACE_DELIMITER
            . \implode(self::NAMESPACE_DELIMITER, $this->toArrayStrings());
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

    /**
     * Returns a lowercased name as unqualified (without the initial `\\`) string.
     *
     * @return non-empty-string
     */
    public function toUnqualifiedLowerString(): string
    {
        return \strtolower($this->toUnqualifiedString());
    }

    /**
     * Returns a lowercased name as full qualified (with the initial `\\`) string.
     *
     * @return non-empty-string
     */
    public function toFullQualifiedLowerString(): string
    {
        return \strtolower($this->toFullQualifiedString());
    }

    /**
     * @return \Traversable<array-key, Identifier>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->parts);
    }

    /**
     * @return int<1, max>
     */
    public function count(): int
    {
        return \count($this->parts);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return array{non-empty-list<Identifier>, int<0, max>, bool}
     */
    public function __serialize(): array
    {
        return [$this->parts, $this->offset, $this->isFullyQualified];
    }

    /**
     * @param array{0?: non-empty-list<Identifier>, 1?: int<0, max>, 2?: bool} $data
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->parts = $parts = $data[0] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Name segments',
        );

        $this->first = \reset($parts);
        $this->last = \end($parts);

        $this->offset = $data[1] ?? 0;
        $this->isFullyQualified = $data[2] ?? self::IS_FULLY_QUALIFIED_DEFAULT_VALUE;
    }
}
