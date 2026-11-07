<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\TypePrinter;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\Tests\TestCase;
use TypeLang\Type\ThisTypeNode;

#[CoversClass(NonPrintableNodeException::class)]
final class NonPrintableNodeExceptionTest extends TestCase
{
    public function testMessageNamesTheNode(): void
    {
        $exception = NonPrintableNodeException::becauseInvalidNodeGiven(new ThisTypeNode());

        self::assertStringContainsString(ThisTypeNode::class, $exception->getMessage());
    }

    public function testPreviousExceptionIsKept(): void
    {
        $previous = new \RuntimeException();
        $exception = NonPrintableNodeException::becauseInvalidNodeGiven(new ThisTypeNode(), $previous);

        self::assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionBelongsToThePrinterExceptions(): void
    {
        $this->expectException(PrinterExceptionInterface::class);

        (new TypePrinter(new NodePrinterRegistry()))->print(new ThisTypeNode());
    }
}
