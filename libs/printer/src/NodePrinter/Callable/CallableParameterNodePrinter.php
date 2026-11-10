<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Callable;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<CallableParameterNode>
 */
final class CallableParameterNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = '';

        if ($node->type !== null) {
            $result = $context->print($node->type);
        }

        if ($node->type !== null && $node->name !== null) {
            $result .= ' ';
        }

        if ($node->isByReference) {
            $result .= '&';
        }

        if ($node->isVariadic) {
            $result .= '...';
        }

        if ($node->name !== null) {
            $result .= $context->print($node->name);
        }

        if ($node->default === null) {
            return $result;
        }

        if ($node->default->value === null) {
            return $result . '=';
        }

        return $result . ' = ' . $context->print($node->default->value);
    }
}
