<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\TypeNode;

abstract class TypePrinter implements TypePrinterInterface
{
    /**
     * @var non-empty-string
     */
    protected const string DEFAULT_NEW_LINE_DELIMITER = "\n";

    /**
     * @var non-empty-string
     */
    protected const string DEFAULT_INDENTION = '    ';

    /**
     * @var int<0, max>
     */
    protected int $depth = 0;

    /**
     * @var int<0, max>
     */
    protected int $nesting = 0;

    public function __construct(
        public readonly string $newLine = self::DEFAULT_NEW_LINE_DELIMITER,
        public readonly string $indention = self::DEFAULT_INDENTION,
    ) {}

    public function print(TypeNode $stmt): string
    {
        $this->nesting = $this->depth = 0;

        return $this->make($stmt);
    }

    abstract protected function make(TypeNode $stmt): string;

    /**
     * @param int<0, max>|null $depth
     */
    protected function prefix(?int $depth = null, bool $force = false): string
    {
        $depth ??= $this->depth;

        if ($depth > 0 || $force === true) {
            return \str_repeat($this->indention, $depth);
        }

        return '';
    }

    /**
     * @template TResult of mixed
     * @param callable():TResult $section
     * @return TResult
     */
    protected function nested(callable $section): mixed
    {
        ++$this->depth;

        $result = $section();

        --$this->depth;

        return $result;
    }

    /**
     * @param LogicalTypeNode<TypeNode> $stmt
     * @return list<non-empty-string>
     */
    protected function unwrapAndPrint(LogicalTypeNode $stmt): iterable
    {
        return $this->printMap($this->unwrap($stmt));
    }

    /**
     * @param iterable<mixed, TypeNode> $stmts
     * @return list<non-empty-string>
     */
    protected function printMap(iterable $stmts): array
    {
        $result = [];

        foreach ($stmts as $stmt) {
            $result[] = $this->make($stmt);
        }

        /** @var list<non-empty-string> */
        return \array_unique($result);
    }

    /**
     * @template T of TypeNode
     * @param LogicalTypeNode<T> $logical
     * @return iterable<array-key, T>
     */
    protected function unwrap(LogicalTypeNode $logical): iterable
    {
        foreach ($logical->statements as $statement) {
            if ($statement instanceof $logical) {
                yield from $this->unwrap($statement);
            } else {
                yield $statement;
            }
        }
    }
}
