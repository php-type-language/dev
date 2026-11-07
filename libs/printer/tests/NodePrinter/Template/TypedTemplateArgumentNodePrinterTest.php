<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TypedTemplateArgumentNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TypedTemplateArgumentNode;

#[CoversClass(TypedTemplateArgumentNodePrinter::class)]
final class TypedTemplateArgumentNodePrinterTest extends NodePrinterTestCase
{
    public function testArgumentIsPrintedAsItsType(): void
    {
        self::assertSame('int', self::pretty(new TypedTemplateArgumentNode(self::named('int'))));
    }

    public function testVarianceIsWrittenBeforeTheType(): void
    {
        $argument = new TypedTemplateArgumentNode(self::named('int'), new Identifier('covariant'));

        self::assertSame('covariant int', self::pretty($argument));
    }

    public function testVarianceIsWrittenTheWayItWasWritten(): void
    {
        $argument = new TypedTemplateArgumentNode(self::named('int'), new Identifier('out'));

        self::assertSame('out int', self::pretty($argument));
    }
}
