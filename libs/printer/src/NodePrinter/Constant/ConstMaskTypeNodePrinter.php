<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Constant;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<ConstMaskTypeNode>
 */
final class ConstMaskTypeNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        $mask = $node->mask->toString();

        // SOME_*
        if ($node->owner === null) {
            return $mask;
        }

        // Some\Any::SOME_*
        if ($node->owner instanceof Name) {
            return $node->owner->toString() . '::' . $mask;
        }

        // \SOME_*
        if ($node->owner->isRoot()) {
            return '\\' . $mask;
        }

        // Some\Any\SOME_*
        return $node->owner->toString() . '\\' . $mask;
    }
}
