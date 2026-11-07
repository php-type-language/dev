<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Node;

/**
 * Prints the native type of a literal.
 *
 * @template-implements NodePrinterInterface<LiteralNode<mixed>>
 */
final class NativeLiteralNodePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return match (true) {
            $node instanceof BoolLiteralNode => 'bool',
            $node instanceof FloatLiteralNode => 'float',
            $node instanceof IntLiteralNode => 'int',
            $node instanceof NullLiteralNode => 'null',
            $node instanceof StringLiteralNode => 'string',
            default => \get_debug_type($node->value),
        };
    }
}
