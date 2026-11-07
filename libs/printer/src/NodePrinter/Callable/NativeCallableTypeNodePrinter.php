<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Callable;

use TypeLang\Printer\NativeTypePrinter\TypeAliases;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Node;

/**
 * Prints the native type the name of a callable stands for, with no
 * parameters and no return type.
 *
 * @template-implements NodePrinterInterface<CallableTypeNode>
 */
final class NativeCallableTypeNodePrinter implements NodePrinterInterface
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
