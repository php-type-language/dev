<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Constant;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ClassConstMaskTypeNode>
 */
final class ClassConstMaskTypeNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $node->class->toString() . '::' . $node->mask->toString();
    }
}
