<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Constant;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ConstMaskTypeNode>
 */
final class ConstMaskTypeNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = $node->mask->toString();

        if ($node->namespace === null) {
            return $result;
        }

        return $node->namespace->toString() . '\\' . $result;
    }
}
