<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;

use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;
use TypeLang\Type\Condition\EqualConditionNode;

/**
 * @template-extends ConditionNodePrinter<EqualConditionNode>
 */
final class EqualConditionNodePrinter extends ConditionNodePrinter
{
    protected function getOperator(): string
    {
        return 'is';
    }
}
