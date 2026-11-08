<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateBoundNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Name;
use TypeLang\Type\Template\TemplateBoundNode;

#[CoversClass(TemplateBoundNodePrinter::class)]
final class TemplateBoundNodePrinterTest extends NodePrinterTestCase
{
    public function testOperatorIsWrittenBeforeTheType(): void
    {
        self::assertSame('of int', self::pretty(new TemplateBoundNode(new Name('of'), self::named('int'))));
    }

    public function testOperatorIsWrittenTheWayItWasWritten(): void
    {
        $bound = new TemplateBoundNode(new Name('as'), self::named('int'));

        self::assertSame('as int', self::pretty($bound));
    }
}
