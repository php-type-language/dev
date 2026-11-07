<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Condition;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Condition\NativeConditionalTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Condition\EqualConditionNode;

#[CoversClass(NativeConditionalTypeNodePrinter::class)]
final class NativeConditionalTypeNodePrinterTest extends NodePrinterTestCase
{
    private static function condition(): EqualConditionNode
    {
        return new EqualConditionNode(self::named('T'), self::named('int'));
    }

    public function testResultsAreJoinedIntoUnion(): void
    {
        $type = new ConditionalTypeNode(self::condition(), self::named('A'), self::named('B'));

        self::assertSame('A|B', self::native($type));
    }

    public function testResultsOfNestedConditionsAreFlattened(): void
    {
        $type = new ConditionalTypeNode(
            self::condition(),
            self::named('A'),
            new ConditionalTypeNode(self::condition(), self::named('B'), self::named('C')),
        );

        self::assertSame('A|B|C', self::native($type));
    }

    public function testResultsOfNestedConditionInTheFirstBranchAreFlattened(): void
    {
        $type = new ConditionalTypeNode(
            self::condition(),
            new ConditionalTypeNode(self::condition(), self::named('A'), self::named('B')),
            self::named('C'),
        );

        self::assertSame('A|B|C', self::native($type));
    }

    public function testSameResultsAreWrittenOnce(): void
    {
        $type = new ConditionalTypeNode(self::condition(), self::named('A'), self::named('A'));

        self::assertSame('A', self::native($type));
    }
}
