<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;

/**
 * Prints the same native type for a node, whatever the node holds.
 *
 * @template-implements NodePrinterInterface<Node>
 */
final class NativeFixedNodePrinter implements NodePrinterInterface
{
    public function __construct(
        /**
         * The type printed for every node.
         *
         * @var non-empty-string
         */
        private readonly string $type,
    ) {}

    public function printNode(Node $node, PrinterContext $context): string
    {
        return $this->type;
    }
}
