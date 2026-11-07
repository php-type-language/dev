<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use TypeLang\Printer\PrinterOptions\UnionSpacing;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\NullableTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

#[CoversClass(NullableTypeNodePrinter::class)]
final class NullableTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testNullableIsWrittenWithTheQuestionMark(): void
    {
        self::assertSame('?int', self::pretty(new NullableTypeNode(self::named('int'))));
    }

    public function testNullableIsWrittenAsUnionWithNull(): void
    {
        $options = new PrinterOptions(nullableStyle: NullableStyle::Union);

        self::assertSame('int|null', self::pretty(new NullableTypeNode(self::named('int')), $options));
    }

    public function testNullableAsUnionFollowsTheUnionFormat(): void
    {
        $options = new PrinterOptions(unionSpacing: UnionSpacing::Spaced, nullableStyle: NullableStyle::Union);

        self::assertSame('int | null', self::pretty(new NullableTypeNode(self::named('int')), $options));
    }

    public function testNullableUnionIsWrapped(): void
    {
        $type = new NullableTypeNode(self::union(self::named('A'), self::named('B')));

        self::assertSame('?(A|B)', self::pretty($type));
    }
}
