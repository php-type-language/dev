<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\NativeLiteralNodePrinter;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;

#[CoversClass(NativeLiteralNodePrinter::class)]
final class NativeLiteralNodePrinterTest extends NodePrinterTestCase
{
    /**
     * @return iterable<non-empty-string, array{LiteralNode<mixed>, non-empty-string}>
     */
    public static function literalsDataProvider(): iterable
    {
        yield 'int' => [new IntLiteralNode(1), 'int'];
        yield 'float' => [new FloatLiteralNode(1.5), 'float'];
        yield 'string' => [new StringLiteralNode('a'), 'string'];
        yield 'bool' => [new BoolLiteralNode(false), 'bool'];
        yield 'null' => [new NullLiteralNode(), 'null'];
    }

    /**
     * @param LiteralNode<mixed> $literal
     */
    #[DataProvider('literalsDataProvider')]
    public function testLiteralIsPrintedAsItsNativeType(LiteralNode $literal, string $expected): void
    {
        self::assertSame($expected, self::native($literal));
    }

    public function testLiteralOfAnotherKindIsPrintedAsTheTypeOfItsValue(): void
    {
        $literal = new class (['a'], '[]') extends LiteralNode {};

        self::assertSame('array', self::native($literal));
    }
}
