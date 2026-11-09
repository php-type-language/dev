<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use TypeLang\Printer\PrinterOptions\UnionSpacing;
use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use TypeLang\Printer\PrinterOptions\CallableReturnSpacing;
use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

#[CoversClass(PrettyTypePrinter::class)]
final class PrettyTypePrinterTest extends TestCase
{
    /**
     * @param non-empty-string $newLine
     * @param non-empty-string $indentation
     * @param int<0, max> $maxInlineShapeFields
     */
    private function printer(
        string $newLine = "\n",
        string $indentation = '    ',
        UnionSpacing $unionSpacing = UnionSpacing::Spaced,
        IntersectionSpacing $intersectionSpacing = IntersectionSpacing::Spaced,
        CallableReturnSpacing $callableReturnSpacing = CallableReturnSpacing::Spaced,
        int $maxInlineShapeFields = 1,
    ): PrettyTypePrinter {
        return new PrettyTypePrinter(new PrinterOptions(
            newLine: $newLine,
            indentation: $indentation,
            unionSpacing: $unionSpacing,
            intersectionSpacing: $intersectionSpacing,
            callableReturnSpacing: $callableReturnSpacing,
            maxInlineShapeFields: $maxInlineShapeFields,
        ));
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
        self::assertSame('int|string', $this->printer(unionSpacing: UnionSpacing::Compact)
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
        self::assertSame('int&string', $this->printer(intersectionSpacing: IntersectionSpacing::Compact)
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
        self::assertSame('array{a: int, b: string, c: float}', $this->printer(maxInlineShapeFields: 5)
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

    public function testPrintCallableType(): void
    {
        self::assertSame('callable(int, string): void', $this->printer()
            ->print(self::parse('callable(int, string): void')));
    }

    public function testPrintCallableWrapsLogicalReturnType(): void
    {
        self::assertSame('callable(int): (int | string)', $this->printer()
            ->print(self::parse('callable(int): (int|string)')));
    }

    public function testPrintUnionOfCallableKeepsReturnTypeUnwrapped(): void
    {
        self::assertSame('callable(int): int | string', $this->printer()
            ->print(self::parse('callable(int): int|string')));
    }

    public function testPrintCallableReturnTypeWithoutWrapping(): void
    {
        self::assertSame('callable(int):void', $this->printer(callableReturnSpacing: CallableReturnSpacing::Compact)
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

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function maskProvider(): iterable
    {
        yield 'a wildcard alone' => ['Foo::*'];
        yield 'a trailing wildcard' => ['Foo::BAR_*'];
        yield 'a leading wildcard' => ['Foo::*_BAR'];
        yield 'a wildcard in between' => ['Foo::A*B'];
        yield 'several wildcards' => ['Foo::BAR*BAZ*SOME'];
        yield 'a global mask' => ['JSON_*_FLAG'];
        yield 'a namespaced global mask' => ['Some\Any\JSON_*'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('maskProvider')]
    public function testPrintMaskOfEverySegmentItIsWrittenOf(string $type): void
    {
        self::assertSame($type, $this->printer()->print(self::parse($type)));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function wildcardAndTemplateProvider(): iterable
    {
        yield 'a wildcard argument' => ['Collection<*>'];
        yield 'a wildcard beside a type' => ['HashMap<array-key, *>'];
        yield 'a template parameter' => ['callable<T>(T): T'];
        yield 'a bounded template parameter' => ['callable<T of Some>(T): T'];
        yield 'every kind of bound' => ['Closure<T of Some, U super Any, V = int>(T, U): V'];
        yield 'several bounds on one parameter' => ['callable<T of Some super Any>(T): void'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('wildcardAndTemplateProvider')]
    public function testPrintWildcardsAndTemplateParameters(string $type): void
    {
        self::assertSame($type, $this->printer()->print(self::parse($type)));
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
    }

    public function testPrintUsesCustomNewLineAndIndentation(): void
    {
        self::assertSame(
            'array{|>>int,|>>string|}',
            $this->printer(newLine: '|', indentation: '>>')
                ->print(self::parse('array{int, string}')),
        );
    }

    public function testPrintResetsStateBetweenCalls(): void
    {
        $printer = $this->printer();
        $type = self::parse('callable(int): (int|string)');

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

    private static function named(string $name): NamedTypeNode
    {
        return new NamedTypeNode(new Name($name));
    }

    /**
     * array<int, string>
     */
    private static function generic(): NamedTypeNode
    {
        return new NamedTypeNode(new Name('array'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::named('int')),
            new TypedTemplateArgumentNode(self::named('string')),
        ]));
    }

    /**
     * callable(int, string): void
     */
    private static function callable(): CallableTypeNode
    {
        return new CallableTypeNode(
            new Name('callable'),
            new CallableParameterListNode([
                new CallableParameterNode(self::named('int')),
                new CallableParameterNode(self::named('string')),
            ]),
            self::named('void'),
        );
    }

    /**
     * array{a: int, b: string}
     */
    private static function shape(): NamedTypeNode
    {
        return new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Identifier('a'), self::named('int')),
            new NamedFieldNode(new Identifier('b'), self::named('string')),
        ]));
    }

    private function assertPrinted(string $expected, PrettyTypePrinter $printer, TypeNode $type): void
    {
        self::assertSame($expected, $printer->print($type));
    }

    public function testTemplateIsSingleLineByDefault(): void
    {
        $this->assertPrinted('array<int, string>', new PrettyTypePrinter(), self::generic());
    }

    public function testTemplateIsMultilineAfterTheThreshold(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(maxInlineTemplateItems: 1));

        $this->assertPrinted("array<\n    int,\n    string\n>", $printer, self::generic());
    }

    public function testTemplateIsSingleLineUpToTheThreshold(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(maxInlineTemplateItems: 2));

        $this->assertPrinted('array<int, string>', $printer, self::generic());
    }

    public function testCallableIsSingleLineByDefault(): void
    {
        $this->assertPrinted('callable(int, string): void', new PrettyTypePrinter(), self::callable());
    }

    public function testCallableIsMultilineAfterTheThreshold(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(maxInlineCallableParameters: 1));

        $this->assertPrinted("callable(\n    int,\n    string\n): void", $printer, self::callable());
    }

    public function testMultilineItemsAreIndentedOneLevelDeeper(): void
    {
        $type = new NamedTypeNode(new Name('array'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::named('int')),
            new TypedTemplateArgumentNode(self::shape()),
        ]));

        $printer = new PrettyTypePrinter(new PrinterOptions(maxInlineShapeFields: 1, maxInlineTemplateItems: 1));

        $this->assertPrinted(
            "array<\n    int,\n    array{\n        a: int,\n        b: string\n    }\n>",
            $printer,
            $type,
        );
    }

    public function testNoSpaceAfterCommaInTemplate(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(commaSpacing: CommaSpacing::Compact));

        $this->assertPrinted('array<int,string>', $printer, self::generic());
    }

    public function testNoSpaceAfterCommaInCallable(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(commaSpacing: CommaSpacing::Compact));

        $this->assertPrinted('callable(int,string): void', $printer, self::callable());
    }

    public function testNoSpaceAfterCommaInSingleLineShape(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(
            maxInlineShapeFields: 2,
            commaSpacing: CommaSpacing::Compact,
        ));

        $this->assertPrinted('array{a: int,b: string}', $printer, self::shape());
    }

    public function testNullableIsShortByDefault(): void
    {
        $this->assertPrinted('?int', new PrettyTypePrinter(), new NullableTypeNode(self::named('int')));
    }

    public function testNullableIsWrittenAsUnionWithNull(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(nullableStyle: NullableStyle::Union));

        $this->assertPrinted('int|null', $printer, new NullableTypeNode(self::named('int')));
    }

    public function testNullableAsUnionFollowsTheUnionFormat(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(
            unionSpacing: UnionSpacing::Spaced,
            nullableStyle: NullableStyle::Union,
        ));

        $this->assertPrinted('int | null', $printer, new NullableTypeNode(self::named('int')));
    }

    public function testListIsWrittenWithBracketsByDefault(): void
    {
        $this->assertPrinted('int[]', new PrettyTypePrinter(), new TypesListTypeNode(self::named('int')));
    }

    public function testListIsWrittenAsGenericArray(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(listStyle: ListStyle::Generic));

        $this->assertPrinted('array<int>', $printer, new TypesListTypeNode(self::named('int')));
    }

    public function testNestedListsFollowTheListStyle(): void
    {
        $printer = new PrettyTypePrinter(new PrinterOptions(listStyle: ListStyle::Generic));
        $type = new TypesListTypeNode(new TypesListTypeNode(self::named('int')));

        $this->assertPrinted('array<array<int>>', $printer, $type);
    }

    private static function union(string ...$names): UnionTypeNode
    {
        return new UnionTypeNode(\array_map(self::named(...), $names));
    }

    private function pretty(TypeNode $type): string
    {
        $options = new PrinterOptions(intersectionSpacing: IntersectionSpacing::Compact);

        return (new PrettyTypePrinter($options))->print($type);
    }

    public function testEveryUnionOfTemplateArgumentsIsWrittenWithNoBrackets(): void
    {
        $type = new NamedTypeNode(new Name('array'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::union('int', 'string')),
            new TypedTemplateArgumentNode(self::union('float', 'bool')),
        ]));

        self::assertSame('array<int|string, float|bool>', $this->pretty($type));
    }

    public function testUnionInsideAnIntersectionIsWrapped(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::union('B', 'C')]);

        self::assertSame('A&(B|C)', $this->pretty($type));
    }

    public function testIntersectionInsideAUnionIsWrapped(): void
    {
        $type = new UnionTypeNode([self::named('A'), new IntersectionTypeNode([self::named('B'), self::named('C')])]);

        self::assertSame('A|(B&C)', $this->pretty($type));
    }

    public function testTemplateArgumentsOfATypeInsideAnIntersectionAreNotWrapped(): void
    {
        $generic = new NamedTypeNode(new Name('Foo'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::union('A', 'B')),
        ]));

        $type = new IntersectionTypeNode([$generic, self::named('C')]);

        self::assertSame('Foo<A|B>&C', $this->pretty($type));
    }

    public function testListOfAUnionIsWrapped(): void
    {
        self::assertSame('(int|string)[]', $this->pretty(new TypesListTypeNode(self::union('int', 'string'))));
    }

    public function testOffsetAccessToAUnionIsWrapped(): void
    {
        $type = new TypeOffsetAccessTypeNode(self::union('A', 'B'), self::named('K'));

        self::assertSame('(A|B)[K]', $this->pretty($type));
    }

    public function testNestingIsNotKeptBetweenSeparateUnions(): void
    {
        $printer = new PrettyTypePrinter();

        self::assertSame('int|string', $printer->print(self::union('int', 'string')));
        self::assertSame('int|string', $printer->print(self::union('int', 'string')));
    }
}
