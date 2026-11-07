<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use TypeLang\Printer\PrinterOptions\CommaSpacing;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\NodePrinter\ThisTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\ThisTypeNode;

#[CoversClass(PrinterContext::class)]
final class PrinterContextTest extends TestCase
{
    private static function context(?PrinterOptions $options = null): PrinterContext
    {
        return new PrinterContext(
            $options ?? new PrinterOptions(),
            new NodePrinterRegistry([ThisTypeNode::class => new ThisTypeNodePrinter()]),
        );
    }

    public function testNodeIsPrintedWithTheRegisteredPrinter(): void
    {
        self::assertSame('$this', self::context()->print(new ThisTypeNode()));
    }

    public function testNodeWithNoPrinterIsRejected(): void
    {
        $this->expectException(NonPrintableNodeException::class);

        (new PrinterContext(new PrinterOptions(), new NodePrinterRegistry()))->print(new ThisTypeNode());
    }

    public function testIndentationGrowsInsideTheIndentedSectionOnly(): void
    {
        $context = self::context(new PrinterOptions(indentation: '--'));

        self::assertSame('', $context->getIndentation());
        $deepest = $context->indent(fn(): string => $context->indent(fn(): string => $context->getIndentation()));

        self::assertSame('----', $deepest);
        self::assertSame('', $context->getIndentation());
    }

    public function testIndentationIsRestoredAfterAnException(): void
    {
        $context = self::context();

        try {
            $context->indent(static function (): never {
                throw new \RuntimeException();
            });
        } catch (\RuntimeException) {
        }

        self::assertSame('', $context->getIndentation());
    }

    public function testLogicalNestingIsKnownInsideTheGroupOnly(): void
    {
        $context = self::context();

        self::assertFalse($context->isInsideLogical());
        self::assertTrue($context->groupLogical(static fn(): bool => $context->isInsideLogical()));
        self::assertFalse($context->isInsideLogical());
    }

    public function testIsolatedSectionIsOutsideOfTheLogicalNesting(): void
    {
        $context = self::context();

        $result = $context->groupLogical(
            static fn(): bool => $context->isolate(static fn(): bool => $context->isInsideLogical()),
        );

        self::assertFalse($result);
        self::assertTrue($context->groupLogical(static fn(): bool => $context->isInsideLogical()));
    }

    public function testDelimiterDependsOnTheSpaceAfterComma(): void
    {
        self::assertSame(', ', self::context()->getDelimiter());
        self::assertSame(',', self::context(new PrinterOptions(commaSpacing: CommaSpacing::Compact))->getDelimiter());
    }

    public function testItemsAreJoinedInOneLineUpToTheThreshold(): void
    {
        $result = self::context()->printItems(static fn(): array => ['a', 'b'], 2, 2);

        self::assertSame('a, b', $result);
    }

    public function testItemsAreWrittenOnePerLineAfterTheThreshold(): void
    {
        $result = self::context()->printItems(static fn(): array => ['a', 'b'], 2, 1);

        self::assertSame("\n    a,\n    b\n", $result);
    }

    public function testNestedMultilineItemsAreIndentedDeeper(): void
    {
        $context = self::context();

        $result = $context->printItems(
            static fn(): array => ['a' . $context->printItems(static fn(): array => ['x', 'y'], 2, 1)],
            1,
            0,
        );

        self::assertSame("\n    a\n        x,\n        y\n    \n", $result);
    }
}
