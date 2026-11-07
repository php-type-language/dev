<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\FieldNode;

/**
 * @template-extends FieldNodePrinter<ComplexFieldNode>
 */
final class ComplexFieldNodePrinter extends FieldNodePrinter
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    protected function printKey(FieldNode $field, PrinterContext $context): string
    {
        return $context->isolate(fn(): string => $context->print($field->key));
    }
}
