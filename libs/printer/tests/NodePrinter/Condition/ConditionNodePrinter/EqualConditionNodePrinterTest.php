<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Condition\ConditionNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter\EqualConditionNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Name;
use TypeLang\Type\VariableNode;

#[CoversClass(EqualConditionNodePrinter::class)]
final class EqualConditionNodePrinterTest extends NodePrinterTestCase
{
    public function testSubjectAndTargetAreJoinedByIs(): void
    {
        $condition = new EqualConditionNode(self::named('T'), self::named('int'));

        self::assertSame('T is int', self::pretty($condition));
    }

    public function testSubjectMayBeAVariable(): void
    {
        $condition = new EqualConditionNode(new VariableNode(new Name('value')), self::named('int'));

        self::assertSame('$value is int', self::pretty($condition));
    }

    public function testTargetMayBeAVariable(): void
    {
        $condition = new EqualConditionNode(self::named('T'), new VariableNode(new Name('value')));

        self::assertSame('T is $value', self::pretty($condition));
    }
}
