<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\UnionSpacing;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;

/**
 * @template-extends LogicalTypeNodePrinter<UnionTypeNode>
 */
final class UnionTypeNodePrinter extends LogicalTypeNodePrinter
{
    protected function getDelimiter(PrinterOptions $options): string
    {
        return match ($options->unionSpacing) {
            UnionSpacing::Compact => '|',
            UnionSpacing::Spaced => ' | ',
        };
    }
}
