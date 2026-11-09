<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Callable;

use TypeLang\Printer\PrinterOptions\CallableReturnSpacing;
use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Callable\CallableTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateBoundNode\BoundKind;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

#[CoversClass(CallableTypeNodePrinter::class)]
final class CallableTypeNodePrinterTest extends NodePrinterTestCase
{
    private static function callable(?TypeNode $return = null, CallableParameterNode ...$parameters): CallableTypeNode
    {
        return new CallableTypeNode(
            new Name('callable'),
            new CallableParameterListNode($parameters),
            $return,
        );
    }

    public function testCallableWithNoParametersAndNoReturnType(): void
    {
        self::assertSame('callable()', self::pretty(self::callable()));
    }

    public function testParametersAreJoinedByComma(): void
    {
        $type = self::callable(
            null,
            new CallableParameterNode(self::named('int')),
            new CallableParameterNode(self::named('string')),
        );

        self::assertSame('callable(int, string)', self::pretty($type));
    }

    public function testReturnTypeFollowsTheColon(): void
    {
        self::assertSame('callable(): void', self::pretty(self::callable(self::named('void'))));
    }

    public function testReturnTypeIsNotSeparatedByWhitespace(): void
    {
        $options = new PrinterOptions(callableReturnSpacing: CallableReturnSpacing::Compact);

        self::assertSame('callable():void', self::pretty(self::callable(self::named('void')), $options));
    }

    public function testReturnedUnionIsWrapped(): void
    {
        $type = self::callable(self::union(self::named('int'), self::named('string')));

        self::assertSame('callable(): (int|string)', self::pretty($type));
    }

    public function testReturnedIntersectionIsWrapped(): void
    {
        $type = self::callable(new IntersectionTypeNode([self::named('A'), self::named('B')]));

        self::assertSame('callable(): (A & B)', self::pretty($type));
    }

    public function testReturnedListOfUnionIsWrappedOnlyOnce(): void
    {
        $type = self::callable(new TypesListTypeNode(self::union(self::named('int'), self::named('string'))));

        self::assertSame('callable(): (int|string)[]', self::pretty($type));
    }

    public function testReturnedNullableWrittenAsUnionIsWrapped(): void
    {
        $type = self::callable(new NullableTypeNode(self::named('int')));

        $options = new PrinterOptions(nullableStyle: NullableStyle::Union);

        self::assertSame('callable(): (int|null)', self::pretty($type, $options));
        self::assertSame('callable(): ?int', self::pretty($type));
    }

    public function testReturnedTypeWithAUnionInItsTemplateArgumentsIsNotWrapped(): void
    {
        $return = new NamedTypeNode(new Name('array'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::union(self::named('A'), self::named('B'))),
        ]));

        self::assertSame('callable(): array<A|B>', self::pretty(self::callable($return)));
    }

    public function testReturnedTypeWithAUnionInItsShapeIsNotWrapped(): void
    {
        $return = new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Identifier('a'), self::union(self::named('A'), self::named('B'))),
        ]));

        self::assertSame('callable(): array{a: A|B}', self::pretty(self::callable($return)));
    }

    public function testTemplateParametersFollowTheName(): void
    {
        $type = new CallableTypeNode(
            new Name('callable'),
            new CallableParameterListNode(),
            self::named('void'),
            new TemplateParameterListNode([
                new TemplateParameterNode(
                    new Identifier('T'),
                    new TemplateBoundNode(self::named('int'), BoundKind::Upper),
                ),
            ]),
        );

        self::assertSame('callable<T of int>(): void', self::pretty($type));
    }

    public function testParametersAreMultilineAfterTheThreshold(): void
    {
        $type = self::callable(
            self::named('void'),
            new CallableParameterNode(self::named('int')),
            new CallableParameterNode(self::named('string')),
        );

        self::assertSame(
            "callable(\n    int,\n    string\n): void",
            self::pretty($type, new PrinterOptions(maxInlineCallableParameters: 1)),
        );
    }

    public function testParametersAreSingleLineUpToTheThreshold(): void
    {
        $type = self::callable(
            null,
            new CallableParameterNode(self::named('int')),
            new CallableParameterNode(self::named('string')),
        );

        $options = new PrinterOptions(maxInlineCallableParameters: 2);

        self::assertSame('callable(int, string)', self::pretty($type, $options));
    }

    public function testNoSpaceAfterCommaBetweenParameters(): void
    {
        $type = self::callable(
            null,
            new CallableParameterNode(self::named('int')),
            new CallableParameterNode(self::named('string')),
        );

        $options = new PrinterOptions(commaSpacing: CommaSpacing::Compact);

        self::assertSame('callable(int,string)', self::pretty($type, $options));
    }
}
