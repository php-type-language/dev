<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;

/**
 * @template-extends LogicalTypeNodePrinter<IntersectionTypeNode>
 */
final class NativeIntersectionTypeNodePrinter extends LogicalTypeNodePrinter
{
    protected function getDelimiter(PrinterOptions $options): string
    {
        return '&';
    }
}
