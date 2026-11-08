<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Callable;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions\CallableReturnSpacing;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

/**
 * @template-implements NodePrinterInterface<CallableTypeNode>
 */
final class CallableTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = $node->name->toString();

        if ($node->templates !== null) {
            $result .= $context->print($node->templates);
        }

        $parameters = static function () use ($node, $context): array {
            $result = [];

            foreach ($node->parameters as $parameter) {
                $result[] = $context->print($parameter);
            }

            return $result;
        };

        $count = \count($node->parameters);
        $maxInline = $context->options->maxInlineCallableParameters;

        $result .= '(' . $context->printItems($parameters, $count, $maxInline) . ')';

        if ($node->type === null) {
            return $result;
        }

        $returnType = $context->isolate(fn(): string => $context->print($node->type));

        if ($this->isWrittenAsUnion($node->type, $context)) {
            $returnType = '(' . $returnType . ')';
        }

        return $result . match ($context->options->callableReturnSpacing) {
            CallableReturnSpacing::Compact => ':',
            CallableReturnSpacing::Spaced => ': ',
        } . $returnType;
    }

    /**
     * Whether a type is written as the types joined by an operator, which
     * a return type of a callable cannot be.
     */
    private function isWrittenAsUnion(TypeNode $type, PrinterContext $context): bool
    {
        return $type instanceof LogicalTypeNode
            || ($type instanceof NullableTypeNode && $context->options->nullableStyle === NullableStyle::Union);
    }
}
