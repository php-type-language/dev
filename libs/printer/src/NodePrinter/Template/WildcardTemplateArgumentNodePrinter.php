<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Template;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;

/**
 * @template-implements NodePrinterInterface<WildcardTemplateArgumentNode>
 */
final class WildcardTemplateArgumentNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return '*';
    }
}
