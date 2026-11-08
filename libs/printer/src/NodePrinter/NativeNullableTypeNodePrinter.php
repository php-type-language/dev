<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

/**
 * Prints a nullable type as the union with a `null`, as the type that stands
 * for a name may be a union itself, or a `mixed` that is nullable already.
 *
 * @template-implements NodePrinterInterface<NullableTypeNode<TypeNode>>
 */
final class NativeNullableTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $context->print(new UnionTypeNode([$node->type, new NamedTypeNode(new Name('null'))]));
    }
}
