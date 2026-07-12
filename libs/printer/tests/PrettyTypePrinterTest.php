<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Type\TypeNode;

#[CoversClass(PrettyTypePrinter::class)]
final class PrettyTypePrinterTest extends TestCase
{
    /**
     * @param non-empty-string $newLine
     * @param non-empty-string $indention
     */
    private function printer(
        string $newLine = "\n",
        string $indention = '    ',
        bool $wrapUnionType = true,
        bool $wrapIntersectionType = true,
        bool $wrapCallableReturnType = true,
        int $multilineShape = 1,
    ): PrettyTypePrinter {
        return new PrettyTypePrinter(
            newLine: $newLine,
            indention: $indention,
            wrapUnionType: $wrapUnionType,
            wrapIntersectionType: $wrapIntersectionType,
            wrapCallableReturnType: $wrapCallableReturnType,
            multilineShape: $multilineShape,
        );
    }

    public function testPrintNamedType(): void
    {
        self::assertSame('int', $this->printer()->print(self::parse('int')));
    }

    public function testPrintNamespacedName(): void
    {
        self::assertSame('Foo\\Bar', $this->printer()->print(self::parse('Foo\\Bar')));
    }

    public function testPrintUnionType(): void
    {
        self::assertSame('int | string', $this->printer()->print(self::parse('int|string')));
    }

    public function testPrintUnionTypeWithoutWrapping(): void
    {
        self::assertSame('int|string', $this->printer(wrapUnionType: false)
            ->print(self::parse('int|string')));
    }

    public function testPrintUnionDeduplicatesMembers(): void
    {
        self::assertSame('int', $this->printer()->print(self::parse('int|int')));
    }

    public function testPrintIntersectionType(): void
    {
        self::assertSame('int & string', $this->printer()->print(self::parse('int&string')));
    }

    public function testPrintIntersectionTypeWithoutWrapping(): void
    {
        self::assertSame('int&string', $this->printer(wrapIntersectionType: false)
            ->print(self::parse('int&string')));
    }

    public function testPrintNestedLogicalTypeIsParenthesized(): void
    {
        self::assertSame('int | (string & Foo)', $this->printer()
            ->print(self::parse('int|(string&Foo)')));
    }

    public function testPrintDeeplyNestedUnionIsFlattened(): void
    {
        self::assertSame('A | B | C | D', $this->printer()
            ->print(self::parse('((A | B) | C) | D')));
    }

    public function testPrintDeeplyNestedIntersectionIsFlattened(): void
    {
        self::assertSame('A & B & C & D', $this->printer()
            ->print(self::parse('((A & B) & C) & D')));
    }

    public function testPrintDeeplyNestedMixedLogicalTypeKeepsParentheses(): void
    {
        self::assertSame('((A | B) & C) | D', $this->printer()
            ->print(self::parse('((A | B) & C) | D')));
    }

    public function testPrintNullableType(): void
    {
        self::assertSame('?int', $this->printer()->print(self::parse('?int')));
    }

    public function testPrintTemplateArguments(): void
    {
        self::assertSame('array<string, int>', $this->printer()
            ->print(self::parse('array<string, int>')));
    }

    public function testPrintTemplateArgumentHint(): void
    {
        self::assertSame('array<covariant int>', $this->printer()
            ->print(self::parse('array<covariant int>')));
    }

    public function testPrintTemplateArgumentAttribute(): void
    {
        self::assertSame('list<#[Foo] int>', $this->printer()
            ->print(self::parse('list<#[Foo] int>')));
    }

    public function testPrintInlineShape(): void
    {
        self::assertSame('array{foo: int}', $this->printer()
            ->print(self::parse('array{foo: int}')));
    }

    public function testPrintMultilineShape(): void
    {
        self::assertSame(
            "array{\n    int,\n    string\n}",
            $this->printer()->print(self::parse('array{int, string}')),
        );
    }

    public function testPrintOptionalShapeField(): void
    {
        self::assertSame('array{foo?: int}', $this->printer()
            ->print(self::parse('array{foo?: int}')));
    }

    public function testPrintUnsealedShape(): void
    {
        self::assertSame('array{...}', $this->printer()
            ->print(self::parse('array{...}')));
    }

    public function testPrintUnsealedShapeWithField(): void
    {
        self::assertSame('array{foo: int, ...}', $this->printer()
            ->print(self::parse('array{foo: int, ...}')));
    }

    public function testPrintUnsealedShapeWithFieldMultiline(): void
    {
        self::assertSame(
            "array{\n    foo: int,\n    bar: string,\n    ...\n}",
            $this->printer()->print(self::parse('array{foo: int, bar: string, ...}')),
        );
    }

    public function testPrintUnsealedShapeWithTypedRest(): void
    {
        self::assertSame('array{foo: int, ...<string, int>}', $this->printer()
            ->print(self::parse('array{foo: int, ...<string, int>}')));
    }

    public function testPrintShapeInlineWhenBelowMultilineThreshold(): void
    {
        self::assertSame('array{a: int, b: string, c: float}', $this->printer(multilineShape: 5)
            ->print(self::parse('array{a: int, b: string, c: float}')));
    }

    public function testPrintNumericShapeKey(): void
    {
        self::assertSame('array{0: int}', $this->printer()->print(self::parse('array{0: int}')));
    }

    public function testPrintStringLiteralShapeKey(): void
    {
        self::assertSame('array{"foo": int}', $this->printer()
            ->print(self::parse('array{"foo": int}')));
    }

    public function testPrintClassConstantShapeKey(): void
    {
        self::assertSame('array{Foo::BAR: int}', $this->printer()
            ->print(self::parse('array{Foo::BAR: int}')));
    }

    public function testPrintClassConstantWildcardMaskShapeKey(): void
    {
        self::assertSame('array{Foo::*: int}', $this->printer()
            ->print(self::parse('array{Foo::*: int}')));
    }

    public function testPrintClassConstantPrefixMaskShapeKey(): void
    {
        self::assertSame('array{Foo::BAR_*: int}', $this->printer()
            ->print(self::parse('array{Foo::BAR_*: int}')));
    }

    public function testPrintConstantMaskShapeKey(): void
    {
        self::assertSame('array{FOO_*: int}', $this->printer()
            ->print(self::parse('array{FOO_*: int}')));
    }

    public function testPrintShapeFieldAttribute(): void
    {
        self::assertSame('array{#[Foo] foo: int}', $this->printer()
            ->print(self::parse('array{#[Foo] foo: int}')));
    }

    public function testPrintShapeFieldAttributeMultiline(): void
    {
        self::assertSame(
            "array{\n    #[Foo]\n    foo: int,\n    bar: string\n}",
            $this->printer()->print(self::parse('array{#[Foo] foo: int, bar: string}')),
        );
    }

    public function testPrintCallableType(): void
    {
        self::assertSame('callable(int, string): void', $this->printer()
            ->print(self::parse('callable(int, string): void')));
    }

    public function testPrintCallableWrapsLogicalReturnType(): void
    {
        self::assertSame('callable(int): (int | string)', $this->printer()
            ->print(self::parse('callable(int): int|string')));
    }

    public function testPrintCallableReturnTypeWithoutWrapping(): void
    {
        self::assertSame('callable(int):void', $this->printer(wrapCallableReturnType: false)
            ->print(self::parse('callable(int): void')));
    }

    public function testPrintVariadicCallableParameter(): void
    {
        self::assertSame('callable(int...): void', $this->printer()
            ->print(self::parse('callable(int...): void')));
    }

    public function testPrintByReferenceCallableParameter(): void
    {
        self::assertSame('callable(int &$foo): void', $this->printer()
            ->print(self::parse('callable(int &$foo): void')));
    }

    public function testPrintOptionalCallableParameter(): void
    {
        self::assertSame('callable(int=): void', $this->printer()
            ->print(self::parse('callable(int=): void')));
    }

    public function testPrintCallableParameterAttribute(): void
    {
        self::assertSame('callable(#[Foo] int): void', $this->printer()
            ->print(self::parse('callable(#[Foo] int): void')));
    }

    public function testPrintCallableParameterMultipleAttributes(): void
    {
        self::assertSame('callable(#[Foo, Bar] int): void', $this->printer()
            ->print(self::parse('callable(#[Foo, Bar] int): void')));
    }

    public function testPrintClassConstant(): void
    {
        self::assertSame('Foo::BAR', $this->printer()->print(self::parse('Foo::BAR')));
    }

    public function testPrintClassConstantMask(): void
    {
        self::assertSame('Foo::BAR_*', $this->printer()->print(self::parse('Foo::BAR_*')));
    }

    public function testPrintClassConstantWildcardMask(): void
    {
        self::assertSame('Foo::*', $this->printer()->print(self::parse('Foo::*')));
    }

    public function testPrintConstantMask(): void
    {
        self::assertSame('FOO_*', $this->printer()->print(self::parse('FOO_*')));
    }

    public function testPrintTypesList(): void
    {
        self::assertSame('int[]', $this->printer()->print(self::parse('int[]')));
    }

    public function testPrintTypeOffsetAccess(): void
    {
        self::assertSame('Foo[Bar]', $this->printer()->print(self::parse('Foo[Bar]')));
    }

    #[DataProvider('literalProvider')]
    public function testPrintLiteral(string $type, string $expected): void
    {
        self::assertSame($expected, $this->printer()->print(self::parse($type)));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function literalProvider(): iterable
    {
        yield 'string' => ['"foo"', '"foo"'];
        yield 'int' => ['123', '123'];
        yield 'float' => ['1.5', '1.5'];
        yield 'true' => ['true', 'true'];
        yield 'false' => ['false', 'false'];
        yield 'null' => ['null', 'null'];
    }

    public function testPrintTernaryExpression(): void
    {
        self::assertSame('($x is int ? string : bool)', $this->printer()
            ->print(self::parse('($x is int ? string : bool)')));
    }

    #[DataProvider('ternaryConditionProvider')]
    public function testPrintTernaryCondition(string $type, string $expected): void
    {
        self::assertSame($expected, $this->printer()->print(self::parse($type)));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function ternaryConditionProvider(): iterable
    {
        yield 'equal' => ['($x is int ? string : bool)', '($x is int ? string : bool)'];
        yield 'not equal' => ['($x is not int ? string : bool)', '($x is not int ? string : bool)'];
        yield 'greater than' => ['($x > 5 ? string : bool)', '($x > 5 ? string : bool)'];
        yield 'greater than or equal' => ['($x >= 5 ? string : bool)', '($x >= 5 ? string : bool)'];
        yield 'less than' => ['($x < 5 ? string : bool)', '($x < 5 ? string : bool)'];
        yield 'less than or equal' => ['($x <= 5 ? string : bool)', '($x <= 5 ? string : bool)'];
    }

    public function testPrintUsesCustomNewLineAndIndention(): void
    {
        self::assertSame(
            'array{|>>int,|>>string|}',
            $this->printer(newLine: '|', indention: '>>')
                ->print(self::parse('array{int, string}')),
        );
    }

    public function testPrintResetsStateBetweenCalls(): void
    {
        $printer = $this->printer();
        $type = self::parse('callable(int): int|string');

        $first = $printer->print($type);
        $second = $printer->print($type);

        self::assertSame($first, $second);
    }

    public function testPrintThrowsOnNonPrintableNode(): void
    {
        $node = new class extends TypeNode {};

        $this->expectException(NonPrintableNodeException::class);

        $this->printer()->print($node);
    }
}
