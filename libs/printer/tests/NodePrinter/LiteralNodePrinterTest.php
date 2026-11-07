<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\LiteralNodePrinter;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;

#[CoversClass(LiteralNodePrinter::class)]
final class LiteralNodePrinterTest extends NodePrinterTestCase
{
    /**
     * @return iterable<non-empty-string, array{LiteralNode<mixed>, non-empty-string}>
     */
    public static function literalsDataProvider(): iterable
    {
        yield 'int in hex' => [new IntLiteralNode(42, kind: IntLiteralKind::Hexadecimal, raw: '0x2A'), '0x2A'];
        yield 'int with separators' => [new IntLiteralNode(1000, raw: '1_000'), '1_000'];
        yield 'float' => [new FloatLiteralNode(1.5, raw: '1.5e0'), '1.5e0'];
        yield 'string in single quotes' => [new StringLiteralNode('a', raw: "'a'"), "'a'"];
        yield 'string in double quotes' => [new StringLiteralNode('a', raw: '"a"'), '"a"'];
        yield 'bool in any case' => [new BoolLiteralNode(true, 'TruE'), 'TruE'];
        yield 'null in any case' => [new NullLiteralNode('NulL'), 'NulL'];
    }

    /**
     * @param LiteralNode<mixed> $literal
     */
    #[DataProvider('literalsDataProvider')]
    public function testLiteralIsPrintedTheWayItWasWritten(LiteralNode $literal, string $expected): void
    {
        self::assertSame($expected, self::pretty($literal));
    }
}
