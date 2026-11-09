<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Template;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateBoundNode;

/**
 * @template-implements NodePrinterInterface<TemplateBoundNode>
 */
final class TemplateBoundNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $node->kind->value . ' ' . $context->print($node->type);
    }
}
