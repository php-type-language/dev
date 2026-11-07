<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Shape\FieldNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ScalarFieldNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Shape\ScalarFieldNode;

#[CoversClass(ScalarFieldNodePrinter::class)]
final class ScalarFieldNodePrinterTest extends NodePrinterTestCase
{
    public function testNumberKeyIsPrintedAsIs(): void
    {
        self::assertSame('0: int', self::pretty(ScalarFieldNode::from(0, self::named('int'))));
    }

    public function testStringKeyIsPrintedInQuotes(): void
    {
        self::assertSame("'some key': int", self::pretty(ScalarFieldNode::from('some key', self::named('int'))));
    }

    public function testKeyIsPrintedTheWayItWasWritten(): void
    {
        $key = new IntLiteralNode(42, kind: IntLiteralKind::Hexadecimal, raw: '0x2A');
        $field = new ScalarFieldNode($key, self::named('int'));

        self::assertSame('0x2A: int', self::pretty($field));
    }

    public function testOriginalQuotesOfAStringKeyAreKept(): void
    {
        $key = new StringLiteralNode('a', StringLiteralKind::DoubleQuoted, '"a"');
        $field = new ScalarFieldNode($key, self::named('int'), true);

        self::assertSame('"a"?: int', self::pretty($field));
    }
}
