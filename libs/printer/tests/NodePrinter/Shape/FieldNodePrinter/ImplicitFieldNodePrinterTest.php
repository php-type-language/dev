<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Shape\FieldNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ImplicitFieldNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Shape\ImplicitFieldNode;

#[CoversClass(ImplicitFieldNodePrinter::class)]
final class ImplicitFieldNodePrinterTest extends NodePrinterTestCase
{
    public function testFieldWithNoKeyIsPrintedAsItsType(): void
    {
        self::assertSame('int', self::pretty(new ImplicitFieldNode(self::named('int'))));
    }
}
