<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;

use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Shape\FieldNode;
use TypeLang\Type\Shape\NamedFieldNode;

/**
 * @template-extends FieldNodePrinter<NamedFieldNode>
 */
final class NamedFieldNodePrinter extends FieldNodePrinter
{
    protected function printKey(FieldNode $field, PrinterContext $context): string
    {
        return $field->key->toString();
    }
}
