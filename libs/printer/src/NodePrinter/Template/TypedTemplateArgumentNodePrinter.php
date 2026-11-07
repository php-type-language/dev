<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Template;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TypedTemplateArgumentNode;

/**
 * @template-implements NodePrinterInterface<TypedTemplateArgumentNode>
 */
final class TypedTemplateArgumentNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = $context->print($node->value);

        if ($node->variance !== null) {
            return $node->variance->toString() . ' ' . $result;
        }

        return $result;
    }
}
