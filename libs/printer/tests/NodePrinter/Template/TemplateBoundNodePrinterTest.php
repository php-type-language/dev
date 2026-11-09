<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateBoundNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateBoundNode\BoundKind;

#[CoversClass(TemplateBoundNodePrinter::class)]
final class TemplateBoundNodePrinterTest extends NodePrinterTestCase
{
    public function testUpperBoundIsWrittenWithOf(): void
    {
        self::assertSame('of int', self::pretty(new TemplateBoundNode(self::named('int'), BoundKind::Upper)));
    }

    public function testLowerBoundIsWrittenWithSuper(): void
    {
        self::assertSame('super int', self::pretty(new TemplateBoundNode(self::named('int'), BoundKind::Lower)));
    }
}
