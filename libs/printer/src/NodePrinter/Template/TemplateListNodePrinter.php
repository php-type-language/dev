<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Template;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TemplateNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;

/**
 * Prints the template arguments or the template parameters of a type in the
 * angle brackets.
 *
 * @template-implements NodePrinterInterface<
 *     TemplateListNode<TemplateNode>|TemplateParameterListNode<TemplateParameterNode>
 * >
 */
final class TemplateListNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $items = static function () use ($node, $context): array {
            $result = [];

            foreach ($node as $item) {
                $result[] = $context->print($item);
            }

            return $result;
        };

        $maxInline = $context->options->maxInlineTemplateItems;

        return '<' . $context->printItems($items, \count($node), $maxInline) . '>';
    }
}
