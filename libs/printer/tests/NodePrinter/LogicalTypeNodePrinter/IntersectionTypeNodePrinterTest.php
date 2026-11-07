<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\IntersectionTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;

#[CoversClass(IntersectionTypeNodePrinter::class)]
final class IntersectionTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testTypesAreJoinedByTheAmpersandWrappedByWhitespaces(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::named('B')]);

        self::assertSame('A & B', self::pretty($type));
    }

    public function testAmpersandIsNotWrappedByWhitespaces(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::named('B'), self::named('C')]);

        $options = new PrinterOptions(intersectionSpacing: IntersectionSpacing::Compact);

        self::assertSame('A&B&C', self::pretty($type, $options));
    }

    public function testRepeatedTypesAreWrittenOnce(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::named('A'), self::named('B')]);

        self::assertSame('A & B', self::pretty($type));
    }
}
