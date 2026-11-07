<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Type\Node;

/**
 * Prints a single kind of node.
 *
 * @template TNode of Node
 */
interface NodePrinterInterface
{
    /**
     * @param TNode $node
     * @throws PrinterExceptionInterface in case of the node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string;
}
