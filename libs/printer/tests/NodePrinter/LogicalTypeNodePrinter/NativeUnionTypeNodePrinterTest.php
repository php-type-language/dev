<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\LogicalTypeNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\NativeUnionTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;

#[CoversClass(NativeUnionTypeNodePrinter::class)]
final class NativeUnionTypeNodePrinterTest extends NodePrinterTestCase
{
    /**
     * @return iterable<non-empty-string, array{list<non-empty-string>, non-empty-string}>
     */
    public static function unionsDataProvider(): iterable
    {
        yield 'plain' => [['int', 'string'], 'int|string'];
        yield 'repeated' => [['int', 'int'], 'int'];
        yield 'mixed swallows the rest' => [['int', 'mixed', 'string'], 'mixed'];
        yield 'true with false' => [['true', 'false'], 'bool'];
        yield 'true with false and the rest' => [['true', 'false', 'string'], 'string|bool'];
        yield 'false with true in any order' => [['false', 'int', 'true'], 'int|bool'];
        yield 'true alone is kept' => [['true', 'int'], 'true|int'];
        yield 'qualified true with false' => [['\true', '\false'], 'bool'];
        yield 'qualified bool pair keeps the rest' => [['\true', '\false', 'int'], 'int|bool'];
        yield 'alias of a union' => [['numeric', 'null'], 'int|float|string|null'];
    }

    /**
     * @param list<non-empty-string> $names
     */
    #[DataProvider('unionsDataProvider')]
    public function testUnionIsPrintedAsTheNativeOne(array $names, string $expected): void
    {
        $types = \array_map(self::named(...), $names);

        self::assertSame($expected, self::native(self::union(...$types)));
    }

    public function testBoolLiteralsAreBoolAndAreWrittenOnce(): void
    {
        $type = self::union(new BoolLiteralNode(true), new BoolLiteralNode(false), self::named('int'));

        self::assertSame('bool|int', self::native($type));
    }

    public function testBracketsKeepTheNestedIntersection(): void
    {
        $type = self::union(
            self::named('A'),
            new IntersectionTypeNode([self::named('B'), self::named('C')]),
        );

        self::assertSame('A|(B&C)', self::native($type));
    }

    public function testNestedLogicalTypesAreWrapped(): void
    {
        $type = self::union(
            new IntersectionTypeNode([
                self::union(self::named('A'), self::named('B')),
                self::named('C'),
            ]),
            self::named('D'),
        );

        self::assertSame('((A|B)&C)|D', self::native($type));
    }
}
