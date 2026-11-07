<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\ThisTypeNodePrinter;
use TypeLang\Type\ThisTypeNode;

#[CoversClass(ThisTypeNodePrinter::class)]
final class ThisTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testThisIsPrintedWithTheDollar(): void
    {
        self::assertSame('$this', self::pretty(new ThisTypeNode()));
    }
}
