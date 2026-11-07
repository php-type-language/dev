<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Shape;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Node;
use TypeLang\Type\Shape\ExplicitFieldNode;
use TypeLang\Type\Shape\FieldNode;

/**
 * Prints a field of a shape: its key, the optional mark and its type.
 *
 * @template TField of FieldNode
 *
 * @template-implements NodePrinterInterface<TField>
 */
abstract class FieldNodePrinter implements NodePrinterInterface
{
    /**
     * Prints a key of the field, or an empty string for a field with none.
     *
     * @param TField $field
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    abstract protected function printKey(FieldNode $field, PrinterContext $context): string;

    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $type = $context->print($node->type);
        $key = $this->printKey($node, $context);

        if ($key === '') {
            return $type;
        }

        $isOptional = $node instanceof ExplicitFieldNode && $node->isOptional;

        return $key . ($isOptional ? '?' : '') . ': ' . $type;
    }
}
