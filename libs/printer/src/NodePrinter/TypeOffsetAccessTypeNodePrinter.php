<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;

/**
 * @template-implements NodePrinterInterface<TypeOffsetAccessTypeNode<TypeNode>>
 */
final class TypeOffsetAccessTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $type = $context->printSuffixBase($node->type);
        $access = $context->isolate(fn(): string => $context->print($node->access));

        return $type . '[' . $access . ']';
    }
}
