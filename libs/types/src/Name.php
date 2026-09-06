<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @phpstan-consistent-constructor
 *
 * @template-implements \IteratorAggregate<array-key, Identifier>
 *
 * @property-read Identifier $first An alias of {@see first()} method.
 * @property-read Identifier $last An alias of {@see last()} method.
 * @property-read bool $isSimple An alias of {@see isSimple()} method.
 * @property-read bool $isSpecial An alias of {@see isSpecial()} method.
 * @property-read bool $isBuiltin An alias of {@see isBuiltin()} method.
 */
final class Name extends Node implements \IteratorAggregate, \Countable, \Stringable
{
    /**
     * @var list<non-empty-string>
     */
    private const VIRTUAL_PROPERTIES = [
        'first',
        'last',
        'isSimple',
        'isSpecial',
        'isBuiltin',
    ];

    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    public const IS_FULLY_QUALIFIED_DEFAULT_VALUE = false;

    /**
     * @var non-empty-list<Identifier>
     */
    public array $segments;

    /**
     * @param iterable<array-key, Identifier> $segments
     */
    public function __construct(
        iterable $segments,
        public readonly bool $isFullyQualified = self::IS_FULLY_QUALIFIED_DEFAULT_VALUE,
    ) {
        $segments = match (true) {
            $segments instanceof \Traversable => \iterator_to_array($segments, false),
            \array_is_list($segments) => $segments,
            default => \array_values($segments),
        };

        \assert($segments !== [], new \InvalidArgumentException('Name segments count can not be empty'));

        $this->segments = $segments;
    }

    /**
     * @param iterable<mixed, non-empty-string|\Stringable> $segments
     */
    public static function createFromStringSegments(
        iterable $segments,
        bool $isFullyQualified = self::IS_FULLY_QUALIFIED_DEFAULT_VALUE,
    ): self {
        $identifiers = [];

        foreach ($segments as $segment) {
            $identifiers[] = Identifier::createFromString($segment);
        }

        return new self($identifiers, $isFullyQualified);
    }

    /**
     * @param non-empty-string|\Stringable $name
     */
    public static function createFromString(string|\Stringable $name): self
    {
        $name = (string) $name;
        $unqualified = \trim($name, self::NAMESPACE_DELIMITER);

        $segments = [];

        foreach (\explode(self::NAMESPACE_DELIMITER, $unqualified) as $segment) {
            if ($segment === '') {
                continue;
            }

            $segments[] = $segment;
        }

        return self::createFromStringSegments(
            segments: $segments,
            isFullyQualified: \str_starts_with($name, self::NAMESPACE_DELIMITER),
        );
    }

    /**
     * @param int<0, max> $offset
     * @param int<0, max>|null $length
     */
    public function slice(int $offset = 0, ?int $length = null): self
    {
        return new self(
            segments: \array_slice($this->segments, $offset, $length),
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
            ...$this->segments,
            ...$name->segments,
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
            ...$this->segments,
            ...\array_slice($name->segments, 1),
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

        return new self($this->segments, true);
    }

    /**
     * Convert name to unqualified name instance.
     */
    public function toUnqualified(): self
    {
        if ($this->isFullyQualified) {
            return new self($this->segments, false);
        }

        return clone $this;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function toStringArray(): array
    {
        $result = [];

        foreach ($this->segments as $identifier) {
            $result[] = $identifier->toString();
        }

        return $result;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function toLowercaseStringArray(): array
    {
        $result = [];

        foreach ($this->segments as $identifier) {
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
        return \implode(self::NAMESPACE_DELIMITER, $this->toStringArray());
    }

    /**
     * Returns a name as full qualified (with the initial `\\`) string.
     *
     * @return non-empty-string
     */
    public function toFullQualifiedString(): string
    {
        return self::NAMESPACE_DELIMITER
            . \implode(self::NAMESPACE_DELIMITER, $this->toStringArray());
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
        return \strtolower($this->toUnqualifiedString());
    }

    /**
     * @return \Traversable<array-key, Identifier>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->segments);
    }

    /**
     * @return int<1, max>
     */
    public function count(): int
    {
        return \count($this->segments);
    }

    /**
     * Gets the first segment of a name
     */
    public function first(): Identifier
    {
        return $this->segments[0];
    }

    /**
     * Gets the last segment of a name
     */
    public function last(): Identifier
    {
        return $this->segments[\count($this->segments) - 1];
    }

    /**
     * Gets whether the name is simple.
     */
    public function isSimple(): bool
    {
        return \count($this->segments) === 1;
    }

    /**
     * Gets {@see true} in case of name contains special class reference.
     */
    public function isSpecial(): bool
    {
        return $this->isSimple() && $this->first()->isSpecial();
    }

    /**
     * Gets {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        return $this->isSimple() && $this->first()->isBuiltin();
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'first' => $this->first(),
            'last' => $this->last(),
            'isSimple' => $this->isSimple(),
            'isSpecial' => $this->isSpecial(),
            'isBuiltin' => $this->isBuiltin(),
            default => throw new \OutOfRangeException(
                message: \sprintf('Undefined property %s::$%s', self::class, $name),
            ),
        };
    }

    public function __isset(string $name): bool
    {
        return \in_array($name, self::VIRTUAL_PROPERTIES, true);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return array{int<0, max>, non-empty-list<Identifier>}
     */
    public function __serialize(): array
    {
        return [$this->offset, $this->segments];
    }

    /**
     * @param array{0?: int<0, max>, 1?: non-empty-list<Identifier>} $data
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->offset = $data[0] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Name offset',
        );

        $this->segments = $data[1] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Name identifier parts',
        );
    }
}
