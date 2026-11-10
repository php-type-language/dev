<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\TypeOffsetAccessTypeNodePrinter;
use TypeLang\Type\TypeOffsetAccessTypeNode;

#[CoversClass(TypeOffsetAccessTypeNodePrinter::class)]
final class TypeOffsetAccessTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testOffsetIsWrittenInBrackets(): void
    {
        $type = new TypeOffsetAccessTypeNode(self::named('Foo'), self::named('K'));

        self::assertSame('Foo[K]', self::pretty($type));
    }

    public function testAccessedUnionIsWrapped(): void
    {
        $type = new TypeOffsetAccessTypeNode(self::union(self::named('A'), self::named('B')), self::named('K'));

        self::assertSame('(A|B)[K]', self::pretty($type));
    }

    public function testUnionOffsetNeedsNoBrackets(): void
    {
        $type = new TypeOffsetAccessTypeNode(self::named('Foo'), self::union(self::named('A'), self::named('B')));

        self::assertSame('Foo[A|B]', self::pretty($type));
    }
}
