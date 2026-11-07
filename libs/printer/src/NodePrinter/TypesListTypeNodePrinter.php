<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * @template-implements NodePrinterInterface<TypesListTypeNode<TypeNode>>
 */
final class TypesListTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return match ($context->options->listStyle) {
            ListStyle::Brackets => $context->groupLogical(fn(): string => $context->print($node->type)) . '[]',
            ListStyle::Generic => 'array<' . $context->isolate(fn(): string => $context->print($node->type)) . '>',
        };
    }
}
