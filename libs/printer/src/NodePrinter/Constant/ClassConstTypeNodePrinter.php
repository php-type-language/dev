<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Constant;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ClassConstTypeNode>
 */
final class ClassConstTypeNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $node->class->toString() . '::' . $node->constant->toString();
    }
}
