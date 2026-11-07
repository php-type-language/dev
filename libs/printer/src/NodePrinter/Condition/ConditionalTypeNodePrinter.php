<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Condition;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ConditionalTypeNode>
 */
final class ConditionalTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $context->isolate(static fn(): string => \vsprintf('(%s ? %s : %s)', [
            $context->print($node->condition),
            $context->print($node->then),
            $context->print($node->else),
        ]));
    }
}
