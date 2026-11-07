<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\TypesListTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

#[CoversClass(TypesListTypeNodePrinter::class)]
final class TypesListTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testListIsWrittenWithBrackets(): void
    {
        self::assertSame('int[]', self::pretty(new TypesListTypeNode(self::named('int'))));
    }

    public function testNestedListsAreWrittenWithBrackets(): void
    {
        $type = new TypesListTypeNode(new TypesListTypeNode(self::named('int')));

        self::assertSame('int[][]', self::pretty($type));
    }

    public function testListOfUnionIsWrapped(): void
    {
        $type = new TypesListTypeNode(self::union(self::named('int'), self::named('string')));

        self::assertSame('(int|string)[]', self::pretty($type));
    }

    public function testListIsWrittenAsGenericArray(): void
    {
        $options = new PrinterOptions(listStyle: ListStyle::Generic);

        self::assertSame('array<int>', self::pretty(new TypesListTypeNode(self::named('int')), $options));
    }

    public function testNestedListsAreWrittenAsGenericArrays(): void
    {
        $options = new PrinterOptions(listStyle: ListStyle::Generic);
        $type = new TypesListTypeNode(new TypesListTypeNode(self::named('int')));

        self::assertSame('array<array<int>>', self::pretty($type, $options));
    }

    public function testListOfUnionNeedsNoBracketsInGenericStyle(): void
    {
        $options = new PrinterOptions(listStyle: ListStyle::Generic);
        $type = new TypesListTypeNode(self::union(self::named('int'), self::named('string')));

        self::assertSame('array<int|string>', self::pretty($type, $options));
    }
}
