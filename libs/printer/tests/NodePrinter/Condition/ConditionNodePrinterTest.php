<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\VariableNode;

#[CoversClass(ConditionNodePrinter::class)]
final class ConditionNodePrinterTest extends NodePrinterTestCase
{
    public function testSubjectAndTargetAreJoinedByIs(): void
    {
        $condition = new ConditionNode(self::named('T'), self::named('int'), ConditionKind::Equal);

        self::assertSame('T is int', self::pretty($condition));
    }

    public function testSubjectAndTargetAreJoinedByIsNot(): void
    {
        $condition = new ConditionNode(self::named('T'), self::named('int'), ConditionKind::NotEqual);

        self::assertSame('T is not int', self::pretty($condition));
    }

    public function testSubjectMayBeAVariable(): void
    {
        $condition = new ConditionNode(new VariableNode('value'), self::named('int'), ConditionKind::Equal);

        self::assertSame('$value is int', self::pretty($condition));
    }

    public function testTargetMayBeAVariable(): void
    {
        $condition = new ConditionNode(self::named('T'), new VariableNode('value'), ConditionKind::Equal);

        self::assertSame('T is $value', self::pretty($condition));
    }
}
