<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\NativeTypePrinter\TypeAliases;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;

/**
 * Prints the native type a name stands for, with no template arguments and
 * no shape fields.
 *
 * @template-implements NodePrinterInterface<NamedTypeNode>
 */
final class NativeNamedTypeNodePrinter implements NodePrinterInterface
{
    public function __construct(
        /**
         * The native types the names stand for.
         */
        private readonly TypeAliases $aliases,
    ) {}

    public function printNode(Node $node, PrinterContext $context): string
    {
        return $this->aliases->resolve($node->name->toString());
    }
}
