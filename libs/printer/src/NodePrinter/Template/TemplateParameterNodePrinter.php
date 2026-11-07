<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Template;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateParameterNode;

/**
 * @template-implements NodePrinterInterface<TemplateParameterNode>
 */
final class TemplateParameterNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = $node->name->toString();

        if ($node->variance !== null) {
            $result = $node->variance->toString() . ' ' . $result;
        }

        if ($node->upper !== null) {
            $result .= ' ' . $context->print($node->upper);
        }

        if ($node->lower !== null) {
            $result .= ' ' . $context->print($node->lower);
        }

        if ($node->default !== null) {
            $result .= ' = ' . $context->print($node->default);
        }

        return $result;
    }
}
