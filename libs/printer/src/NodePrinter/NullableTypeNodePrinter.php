<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

/**
 * @template-implements NodePrinterInterface<NullableTypeNode<TypeNode>>
 */
final class NullableTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return match ($context->options->nullableStyle) {
            NullableStyle::Question => '?' . $this->printOperand($node->type, $context),
            NullableStyle::Union => $context->print(new UnionTypeNode([
                $node->type,
                new NamedTypeNode(new Name('null')),
            ])),
        };
    }

    /**
     * A nullable type of a nullable one is grouped: `?(?int)`, the `??int`
     * is no type.
     *
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    private function printOperand(TypeNode $type, PrinterContext $context): string
    {
        $printed = $context->groupLogical(fn(): string => $context->print($type));

        return $type instanceof NullableTypeNode ? '(' . $printed . ')' : $printed;
    }
}
