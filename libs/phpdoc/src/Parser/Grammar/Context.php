<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar;

use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * The state shared by the rules of a single match: the {@see Cursor}, the
 * {@see Grammar}, the description parser and the collected captures.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\PhpDoc
 */
final class Context
{
    /**
     * @var list<array{string, mixed}>
     */
    private array $captures = [];

    public function __construct(
        public readonly Cursor $cursor,
        public readonly Grammar $grammar,
        public readonly DescriptionParserInterface $descriptions,
    ) {}

    public function capture(string $alias, mixed $value): void
    {
        $this->captures[] = [$alias, $value];
    }

    /**
     * Snapshots the current cursor position and capture count.
     *
     * @return array{int<0, max>, int<0, max>}
     */
    public function mark(): array
    {
        return [$this->cursor->tell(), \count($this->captures)];
    }

    /**
     * Restores a snapshot taken by {@see mark()}, discarding any cursor
     * movement and captures made in between.
     *
     * @param array{int<0, max>, int<0, max>} $snapshot
     */
    public function rollback(array $snapshot): void
    {
        [$position, $length] = $snapshot;

        $this->cursor->seek($position);

        if (\count($this->captures) > $length) {
            $this->captures = \array_slice($this->captures, 0, $length);
        }
    }

    public function result(): MatchedResult
    {
        $grouped = [];

        foreach ($this->captures as [$alias, $value]) {
            $grouped[$alias][] = $value;
        }

        return new MatchedResult($grouped);
    }
}
