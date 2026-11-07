<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;

use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;
use TypeLang\Type\Condition\NotEqualConditionNode;

/**
 * @template-extends ConditionNodePrinter<NotEqualConditionNode>
 */
final class NotEqualConditionNodePrinter extends ConditionNodePrinter
{
    protected function getOperator(): string
    {
        return 'is not';
    }
}
