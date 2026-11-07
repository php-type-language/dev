<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\NativeNullableTypeNodePrinter;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

#[CoversClass(NativeNullableTypeNodePrinter::class)]
final class NativeNullableTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testNullableIsPrintedAsUnionWithNull(): void
    {
        self::assertSame('int|null', self::native(new NullableTypeNode(self::named('int'))));
    }

    public function testNullableAliasOfUnionKeepsTheNull(): void
    {
        self::assertSame('int|float|string|null', self::native(new NullableTypeNode(self::named('numeric'))));
    }

    public function testNullableMixedIsMixed(): void
    {
        self::assertSame('mixed', self::native(new NullableTypeNode(self::named('mixed'))));
    }

    public function testNullableNullIsNull(): void
    {
        self::assertSame('null', self::native(new NullableTypeNode(self::named('null'))));
    }
}
