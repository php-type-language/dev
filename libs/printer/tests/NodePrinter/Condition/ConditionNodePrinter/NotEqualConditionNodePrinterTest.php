<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Condition\ConditionNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter\NotEqualConditionNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Condition\NotEqualConditionNode;

#[CoversClass(NotEqualConditionNodePrinter::class)]
final class NotEqualConditionNodePrinterTest extends NodePrinterTestCase
{
    public function testSubjectAndTargetAreJoinedByIsNot(): void
    {
        $condition = new NotEqualConditionNode(self::named('T'), self::named('int'));

        self::assertSame('T is not int', self::pretty($condition));
    }
}
