<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;

/**
 * @template-extends LogicalTypeNodePrinter<IntersectionTypeNode>
 */
final class IntersectionTypeNodePrinter extends LogicalTypeNodePrinter
{
    protected function getDelimiter(PrinterOptions $options): string
    {
        return match ($options->intersectionSpacing) {
            IntersectionSpacing::Compact => '&',
            IntersectionSpacing::Spaced => ' & ',
        };
    }
}
