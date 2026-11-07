<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\VariableNode;

/**
 * @template-implements NodePrinterInterface<VariableNode>
 */
final class VariableNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return '$' . $node->name->toString();
    }
}
