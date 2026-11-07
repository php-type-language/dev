<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Node;

/**
 * Prints a literal the way it was written.
 *
 * @template-implements NodePrinterInterface<LiteralNode<mixed>>
 */
final class LiteralNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $node->raw;
    }
}
