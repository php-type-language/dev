<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Constant;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ConstTypeNode>
 */
final class ConstTypeNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        $constant = $node->constant->toString();

        // Some\Any::CONST_NAME
        if ($node->owner instanceof Name) {
            return $node->owner->toString() . '::' . $constant;
        }

        // \CONST_NAME
        if ($node->owner->isRoot()) {
            return '\\' . $constant;
        }

        // Some\Any\CONST_NAME
        return $node->owner->toString() . '\\' . $constant;
    }
}
