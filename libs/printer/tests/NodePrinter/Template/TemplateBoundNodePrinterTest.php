<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateBoundNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TemplateBoundNode;

#[CoversClass(TemplateBoundNodePrinter::class)]
final class TemplateBoundNodePrinterTest extends NodePrinterTestCase
{
    public function testUpperBoundIsWrittenWithOf(): void
    {
        self::assertSame('of int', self::pretty(new TemplateBoundNode(self::named('int'), new Identifier('of'))));
    }

    public function testUpperBoundIsWrittenWithAs(): void
    {
        self::assertSame('as int', self::pretty(new TemplateBoundNode(self::named('int'), new Identifier('as'))));
    }

    public function testLowerBoundIsWrittenWithSuper(): void
    {
        self::assertSame('super int', self::pretty(new TemplateBoundNode(self::named('int'), new Identifier('super'))));
    }
}
