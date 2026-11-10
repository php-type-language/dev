<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Condition\ConditionalTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\VariableNode;

#[CoversClass(ConditionalTypeNodePrinter::class)]
final class ConditionalTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testConditionAndBothResultsAreWrittenInBrackets(): void
    {
        $type = new ConditionalTypeNode(
            new ConditionNode(new VariableNode('x'), self::named('int'), ConditionKind::Equal),
            self::named('A'),
            self::named('B'),
        );

        self::assertSame('($x is int ? A : B)', self::pretty($type));
    }

    public function testNestedConditionalTypeIsKeptInItsBrackets(): void
    {
        $type = new ConditionalTypeNode(
            new ConditionNode(self::named('T'), self::named('int'), ConditionKind::NotEqual),
            self::named('A'),
            new ConditionalTypeNode(
                new ConditionNode(self::named('T'), self::named('string'), ConditionKind::Equal),
                self::named('B'),
                self::named('C'),
            ),
        );

        self::assertSame('(T is not int ? A : (T is string ? B : C))', self::pretty($type));
    }

    public function testUnionsInsideNeedNoBrackets(): void
    {
        $type = new ConditionalTypeNode(
            new ConditionNode(self::named('T'), self::union(self::named('int'), self::named('string')), ConditionKind::Equal),
            self::union(self::named('A'), self::named('B')),
            self::named('C'),
        );

        self::assertSame('(T is int|string ? A|B : C)', self::pretty($type));
    }
}
