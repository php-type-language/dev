<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\NamedTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;

#[CoversClass(NamedTypeNodePrinter::class)]
final class NamedTypeNodePrinterTest extends NodePrinterTestCase
{
    private static function arguments(string ...$types): TemplateListNode
    {
        return new TemplateListNode(\array_map(
            static fn(string $type): TypedTemplateArgumentNode => new TypedTemplateArgumentNode(self::named($type)),
            $types,
        ));
    }

    public function testBareNameIsPrintedAsIs(): void
    {
        self::assertSame('Some\Any', self::pretty(new NamedTypeNode(new Name('Any', new NamespaceName('Some')))));
        self::assertSame('\Some\Any', self::pretty(new NamedTypeNode(new Name('Any', new NamespaceName('Some', NamespaceKind::FullyQualified)))));
    }

    public function testTemplateArgumentsFollowTheName(): void
    {
        $type = new NamedTypeNode(new Name('array'), self::arguments('int', 'string'));

        self::assertSame('array<int, string>', self::pretty($type));
    }

    public function testShapeFollowsTheName(): void
    {
        $type = new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Name('a'), self::named('int')),
        ]));

        self::assertSame('array{a: int}', self::pretty($type));
        self::assertSame("array{\n    a: int\n}", self::pretty($type, new PrinterOptions(maxInlineShapeFields: 0)));
    }

    public function testEmptyShapeIsPrintedWithEmptyBraces(): void
    {
        $type = new NamedTypeNode(new Name('array'), null, new FieldListNode([]));

        self::assertSame('array{}', self::pretty($type));
    }

    public function testUnsealedShapeEndsInEllipsis(): void
    {
        $type = new NamedTypeNode(new Name('array'), null, new FieldListNode([], false));

        self::assertSame('array{...}', self::pretty($type));
    }

    public function testUnsealedShapeWithTemplateArgumentsCarriesThemAfterTheEllipsis(): void
    {
        $type = new NamedTypeNode(
            new Name('array'),
            self::arguments('string', 'int'),
            new FieldListNode([new NamedFieldNode(new Name('a'), self::named('int'))]),
        );

        $options = new PrinterOptions(maxInlineShapeFields: 5);

        self::assertSame('array{a: int, ...<string, int>}', self::pretty($type, $options));
    }

    public function testShapeIsMultilineAfterTheThreshold(): void
    {
        $type = new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Name('a'), self::named('int')),
            new NamedFieldNode(new Name('b'), self::named('string')),
        ]));

        self::assertSame('array{a: int, b: string}', self::pretty($type, new PrinterOptions(maxInlineShapeFields: 2)));
        self::assertSame(
            "array{\n    a: int,\n    b: string\n}",
            self::pretty($type, new PrinterOptions(maxInlineShapeFields: 1)),
        );
    }

    public function testNestedShapeIsIndentedDeeper(): void
    {
        $inner = new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Name('x'), self::named('int')),
        ]));
        $type = new NamedTypeNode(new Name('array'), null, new FieldListNode([
            new NamedFieldNode(new Name('a'), $inner),
        ]));

        self::assertSame(
            "array{\n    a: array{\n        x: int\n    }\n}",
            self::pretty($type, new PrinterOptions(maxInlineShapeFields: 0)),
        );
    }
}
