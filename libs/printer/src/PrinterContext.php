<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistryInterface;
use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Type\Node;

/**
 * The state of a single print: the settings, the printers of the nodes and
 * the position in the tree being printed.
 */
final class PrinterContext
{
    /**
     * The number of the indentation levels.
     *
     * @var int<0, max>
     */
    private int $depth = 0;

    /**
     * The number of the logical types (unions and intersections) the node
     * being printed is nested into.
     *
     * @var int<0, max>
     */
    private int $nesting = 0;

    public function __construct(
        /**
         * The formatting settings of the print.
         */
        public readonly PrinterOptions $options,
        /**
         * The printers of the nodes.
         */
        private readonly NodePrinterRegistryInterface $registry,
    ) {}

    /**
     * Prints a node with the printer registered for it.
     *
     * @throws PrinterExceptionInterface in case of the node cannot be printed
     */
    public function print(Node $node): string
    {
        /** @var NodePrinterInterface<Node> */
        $printer = $this->registry->getPrinter($node);

        return $printer->printNode($node, $this);
    }

    /**
     * The indentation of the current level.
     */
    public function getIndentation(): string
    {
        return \str_repeat($this->options->indentation, $this->depth);
    }

    /**
     * The delimiter between the items of a single-line list.
     *
     * @return non-empty-string
     */
    public function getDelimiter(): string
    {
        return match ($this->options->commaSpacing) {
            CommaSpacing::Compact => ',',
            CommaSpacing::Spaced => ', ',
        };
    }

    /**
     * Whether the node being printed is nested into a union or an intersection.
     */
    public function isInsideLogical(): bool
    {
        return $this->nesting > 0;
    }

    /**
     * Runs a section one indentation level deeper.
     *
     * @template TResult of mixed
     * @param \Closure(): TResult $section
     * @return TResult
     */
    public function indent(\Closure $section): mixed
    {
        $depth = $this->depth;
        $this->depth = $depth + 1;

        try {
            return $section();
        } finally {
            $this->depth = $depth;
        }
    }

    /**
     * Runs a section as the content of a union or an intersection.
     *
     * @template TResult of mixed
     * @param \Closure(): TResult $section
     * @return TResult
     */
    public function groupLogical(\Closure $section): mixed
    {
        $nesting = $this->nesting;
        $this->nesting = $nesting + 1;

        try {
            return $section();
        } finally {
            $this->nesting = $nesting;
        }
    }

    /**
     * Runs a section as the content of a bracket pair, where a union or an
     * intersection needs no brackets of its own.
     *
     * @template TResult of mixed
     * @param \Closure(): TResult $section
     * @return TResult
     */
    public function isolate(\Closure $section): mixed
    {
        $nesting = $this->nesting;
        $this->nesting = 0;

        try {
            return $section();
        } finally {
            $this->nesting = $nesting;
        }
    }

    /**
     * Joins the items of a list. A list of more items than the maximum of
     * the single-line one is written one item per line, and its items are
     * printed one level deeper.
     *
     * @param \Closure(): list<string> $items
     * @param int<0, max> $count
     * @param int<0, max> $maxInline
     * @throws PrinterExceptionInterface in case of an item cannot be printed
     */
    public function printItems(\Closure $items, int $count, int $maxInline): string
    {
        if ($count <= $maxInline) {
            return \implode($this->getDelimiter(), $this->isolate($items));
        }

        $lines = $this->indent(function () use ($items): array {
            $prefix = $this->getIndentation();

            return \array_map(
                static fn(string $item): string => $prefix . $item,
                $this->isolate($items),
            );
        });

        return $this->options->newLine
            . \implode(',' . $this->options->newLine, $lines)
            . $this->options->newLine
            . $this->getIndentation();
    }
}
