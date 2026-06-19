<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Parser\Node\Statement;
use TypeLang\Parser\Node\Stmt\LogicalTypeNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

abstract class Printer implements PrinterInterface
{
    /**
     * @var non-empty-string
     */
    protected const DEFAULT_NEW_LINE_DELIMITER = "\n";

    /**
     * @var non-empty-string
     */
    protected const DEFAULT_INDENTION = '    ';

    /**
     * @var int<0, max>
     */
    protected int $depth = 0;

    /**
     * @var int<0, max>
     */
    protected int $nesting = 0;

    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $newLine = self::DEFAULT_NEW_LINE_DELIMITER,
        /**
         * @var non-empty-string
         */
        public readonly string $indention = self::DEFAULT_INDENTION,
    ) {}

    public function print(Statement $stmt): string
    {
        $this->nesting = $this->depth = 0;

        return $this->make($stmt);
    }

    /**
     * @return non-empty-string
     */
    abstract protected function make(Statement $stmt): string;

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
     *
     * @param callable():TResult $section
     *
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
     * @param LogicalTypeNode<TypeStatement> $stmt
     *
     * @return list<non-empty-string>
     */
    protected function unwrapAndPrint(LogicalTypeNode $stmt): iterable
    {
        return $this->printMap($this->unwrap($stmt));
    }

    /**
     * @param iterable<mixed, Statement> $stmts
     *
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
     * @template T of TypeStatement
     *
     * @param LogicalTypeNode<T> $logical
     *
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
