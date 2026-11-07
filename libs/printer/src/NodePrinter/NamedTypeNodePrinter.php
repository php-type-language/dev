<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\Shape\FieldListNode;

/**
 * @template-implements NodePrinterInterface<NamedTypeNode>
 */
final class NamedTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $result = $node->name->toString();

        if ($node->fields !== null) {
            return $result . $this->printShape($node, $node->fields, $context);
        }

        if ($node->arguments !== null) {
            return $result . $context->print($node->arguments);
        }

        return $result;
    }

    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    private function printShape(NamedTypeNode $node, FieldListNode $shape, PrinterContext $context): string
    {
        $fields = static function () use ($node, $shape, $context): array {
            $result = [];

            foreach ($shape->items as $field) {
                $result[] = $context->print($field);
            }

            if (!$shape->isSealed || $node->arguments !== null) {
                $unsealed = '...';

                if ($node->arguments !== null) {
                    $unsealed .= $context->print($node->arguments);
                }

                $result[] = $unsealed;
            }

            return $result;
        };

        $count = \count($shape->items);
        $maxInline = $context->options->maxInlineShapeFields;

        return '{' . $context->printItems($fields, $count, $maxInline) . '}';
    }
}
