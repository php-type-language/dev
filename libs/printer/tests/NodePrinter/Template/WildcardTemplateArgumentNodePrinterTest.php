<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\WildcardTemplateArgumentNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;

#[CoversClass(WildcardTemplateArgumentNodePrinter::class)]
final class WildcardTemplateArgumentNodePrinterTest extends NodePrinterTestCase
{
    public function testWildcardIsPrintedAsTheAsterisk(): void
    {
        self::assertSame('*', self::pretty(new WildcardTemplateArgumentNode()));
    }
}
