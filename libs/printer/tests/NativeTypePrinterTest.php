<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\NativeTypePrinter;
use TypeLang\Type\TypeNode;

#[CoversClass(NativeTypePrinter::class)]
final class NativeTypePrinterTest extends TestCase
{
    public function testPrintTypesListAsIterable(): void
    {
        self::assertSame('iterable', new NativeTypePrinter()->print(self::parse('int[]')));
    }

    public function testPrintShapeAsPlainName(): void
    {
        self::assertSame('array', new NativeTypePrinter()->print(self::parse('array{foo: int}')));
    }

    public function testPrintTemplateTypeUsesNameOnly(): void
    {
        self::assertSame('array', new NativeTypePrinter()->print(self::parse('array<string, int>')));
    }

    public function testPrintUnionTypeWithoutWhitespaces(): void
    {
        self::assertSame('int|string', new NativeTypePrinter()->print(self::parse('int|string')));
    }

    public function testPrintUnionCollapsesMixed(): void
    {
        self::assertSame('mixed', new NativeTypePrinter()->print(self::parse('int|mixed')));
    }

    public function testPrintUnionCollapsesTrueAndFalseToBool(): void
    {
        self::assertSame('bool', new NativeTypePrinter()->print(self::parse('true|false')));
    }

    public function testPrintUnionCollapsesBoolPairKeepingOtherMembers(): void
    {
        self::assertSame('bool|int', new NativeTypePrinter()->print(self::parse('true|false|int')));
    }

    public function testPrintUnionCollapsesNamedTrueAndFalseToBool(): void
    {
        self::assertSame('bool', new NativeTypePrinter()->print(self::parse('\true|\false')));
    }

    public function testPrintUnionCollapsesNamedBoolPairKeepingOtherMembers(): void
    {
        self::assertSame('int|bool', new NativeTypePrinter()->print(self::parse('\true|\false|int')));
    }

    public function testPrintUnionDeduplicatesMembers(): void
    {
        self::assertSame('int', new NativeTypePrinter()->print(self::parse('int|int')));
    }

    public function testPrintNestedLogicalTypeIsParenthesized(): void
    {
        self::assertSame('int|(string&Foo)', new NativeTypePrinter()
            ->print(self::parse('int|(string&Foo)')));
    }

    public function testPrintDeeplyNestedUnionIsFlattened(): void
    {
        self::assertSame('A|B|C|D', new NativeTypePrinter()
            ->print(self::parse('((A | B) | C) | D')));
    }

    public function testPrintDeeplyNestedIntersectionIsFlattened(): void
    {
        self::assertSame('A&B&C&D', new NativeTypePrinter()
            ->print(self::parse('((A & B) & C) & D')));
    }

    public function testPrintDeeplyNestedMixedLogicalTypeKeepsParentheses(): void
    {
        self::assertSame('((A|B)&C)|D', new NativeTypePrinter()
            ->print(self::parse('((A | B) & C) | D')));
    }

    public function testPrintIntersectionTypeWithoutWhitespaces(): void
    {
        self::assertSame('int&string', new NativeTypePrinter()->print(self::parse('int&string')));
    }

    public function testPrintCallableTypeAsName(): void
    {
        self::assertSame('callable', new NativeTypePrinter()
            ->print(self::parse('callable(int, string): void')));
    }

    public function testPrintTernaryAsUnionOfBranches(): void
    {
        self::assertSame('string|bool', new NativeTypePrinter()
            ->print(self::parse('($x is int ? string : bool)')));
    }

    public function testPrintClassConstantAsMixed(): void
    {
        self::assertSame('mixed', new NativeTypePrinter()->print(self::parse('Foo::BAR')));
    }

    public function testPrintClassConstantMaskAsMixed(): void
    {
        self::assertSame('mixed', new NativeTypePrinter()->print(self::parse('Foo::*')));
    }

    public function testPrintConstantMaskAsMixed(): void
    {
        self::assertSame('mixed', new NativeTypePrinter()->print(self::parse('FOO_*')));
    }

    public function testPrintTypeOffsetAccessAsMixed(): void
    {
        self::assertSame('mixed', new NativeTypePrinter()->print(self::parse('Foo[Bar]')));
    }

    public function testPrintThisVariableAsSelf(): void
    {
        self::assertSame('self', new NativeTypePrinter()
            ->print(self::parse('($this is int ? $this : $this)')));
    }

    #[DataProvider('literalProvider')]
    public function testPrintLiteralAsNativeType(string $type, string $expected): void
    {
        self::assertSame($expected, new NativeTypePrinter()->print(self::parse($type)));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function literalProvider(): iterable
    {
        yield 'string' => ['"foo"', 'string'];
        yield 'int' => ['123', 'int'];
        yield 'float' => ['1.5', 'float'];
        yield 'true' => ['true', 'bool'];
        yield 'false' => ['false', 'bool'];
        yield 'null' => ['null', 'null'];
    }

    public function testPrintUsesPreloadedAlias(): void
    {
        self::assertSame('int', new NativeTypePrinter()->print(self::parse('positive-int')));
    }

    public function testAddTypeAlias(): void
    {
        $printer = new NativeTypePrinter();
        $printer->addTypeAlias('Foo', 'bar');

        self::assertSame('bar', $printer->print(self::parse('Foo')));
    }

    public function testTypeAliasIsCaseInsensitive(): void
    {
        $printer = new NativeTypePrinter();
        $printer->addTypeAlias('Foo', 'bar');

        self::assertSame('bar', $printer->print(self::parse('FOO')));
    }

    public function testAddUnionTypeAliasSortsAndJoinsMembers(): void
    {
        $printer = new NativeTypePrinter();
        $printer->addUnionTypeAlias('MyType', ['string', 'int']);

        self::assertSame('int|string', $printer->print(self::parse('MyType')));
    }

    public function testAddIntersectionTypeAliasSortsAndJoinsMembers(): void
    {
        $printer = new NativeTypePrinter();
        $printer->addIntersectionTypeAlias('MyType', ['B', 'A']);

        self::assertSame('A&B', $printer->print(self::parse('MyType')));
    }

    public function testConstructorAliases(): void
    {
        $printer = new NativeTypePrinter(['Custom' => 'int']);

        self::assertSame('int', $printer->print(self::parse('Custom')));
    }

    public function testPrintThrowsOnNonPrintableNode(): void
    {
        $node = new class extends TypeNode {};

        $this->expectException(NonPrintableNodeException::class);

        new NativeTypePrinter()->print($node);
    }
}
