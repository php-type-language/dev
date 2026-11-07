<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\LogicalTypeNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\NativeIntersectionTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;

#[CoversClass(NativeIntersectionTypeNodePrinter::class)]
final class NativeIntersectionTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testTypesAreJoinedByTheAmpersandWithNoWhitespaces(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::named('B'), self::named('C')]);

        self::assertSame('A&B&C', self::native($type));
    }

    public function testRepeatedTypesAreWrittenOnce(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::named('A'), self::named('B')]);

        self::assertSame('A&B', self::native($type));
    }
}
