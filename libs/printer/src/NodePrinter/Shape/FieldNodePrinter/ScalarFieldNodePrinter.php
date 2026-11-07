<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;

use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Shape\FieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;

/**
 * Prints a number or a string key the way it was written: a `0x2A` does not
 * come out as a `42`.
 *
 * @template-extends FieldNodePrinter<ScalarFieldNode>
 */
final class ScalarFieldNodePrinter extends FieldNodePrinter
{
    protected function printKey(FieldNode $field, PrinterContext $context): string
    {
        return $field->key->raw;
    }
}
