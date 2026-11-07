<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Condition;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Node;

/**
 * Prints the condition of a conditional type: both its sides and the
 * operator between them.
 *
 * @template TCondition of ConditionNode
 *
 * @template-implements NodePrinterInterface<TCondition>
 */
abstract class ConditionNodePrinter implements NodePrinterInterface
{
    /**
     * @return non-empty-string
     */
    abstract protected function getOperator(): string;

    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return \vsprintf('%s %s %s', [
            $context->print($node->subject),
            $this->getOperator(),
            $context->print($node->target),
        ]);
    }
}
