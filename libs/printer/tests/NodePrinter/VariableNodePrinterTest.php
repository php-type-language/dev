<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\VariableNodePrinter;
use TypeLang\Type\Name;
use TypeLang\Type\VariableNode;

#[CoversClass(VariableNodePrinter::class)]
final class VariableNodePrinterTest extends NodePrinterTestCase
{
    public function testVariableIsPrintedWithTheDollar(): void
    {
        self::assertSame('$value', self::pretty(new VariableNode(new Name('value'))));
    }
}
