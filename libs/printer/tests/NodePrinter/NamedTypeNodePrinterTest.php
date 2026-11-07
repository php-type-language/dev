<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\NamedTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;
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
        self::assertSame('Some\Any', self::pretty(NamedTypeNode::from('Some\Any')));
        self::assertSame('\Some\Any', self::pretty(NamedTypeNode::from('\Some\Any')));
    }

    public function testTemplateArgumentsFollowTheName(): void
    {
        $type = new NamedTypeNode(TypeName::from('array'), self::arguments('int', 'string'));

        self::assertSame('array<int, string>', self::pretty($type));
    }

    public function testShapeFollowsTheName(): void
    {
        $type = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([
            NamedFieldNode::from('a', self::named('int')),
        ]));

        self::assertSame('array{a: int}', self::pretty($type));
        self::assertSame("array{\n    a: int\n}", self::pretty($type, new PrinterOptions(maxInlineShapeFields: 0)));
    }

    public function testEmptyShapeIsPrintedWithEmptyBraces(): void
    {
        $type = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([]));

        self::assertSame('array{}', self::pretty($type));
    }

    public function testUnsealedShapeEndsInEllipsis(): void
    {
        $type = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([], false));

        self::assertSame('array{...}', self::pretty($type));
    }

    public function testUnsealedShapeWithTemplateArgumentsCarriesThemAfterTheEllipsis(): void
    {
        $type = new NamedTypeNode(
            TypeName::from('array'),
            self::arguments('string', 'int'),
            new FieldListNode([NamedFieldNode::from('a', self::named('int'))]),
        );

        $options = new PrinterOptions(maxInlineShapeFields: 5);

        self::assertSame('array{a: int, ...<string, int>}', self::pretty($type, $options));
    }

    public function testShapeIsMultilineAfterTheThreshold(): void
    {
        $type = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([
            NamedFieldNode::from('a', self::named('int')),
            NamedFieldNode::from('b', self::named('string')),
        ]));

        self::assertSame('array{a: int, b: string}', self::pretty($type, new PrinterOptions(maxInlineShapeFields: 2)));
        self::assertSame(
            "array{\n    a: int,\n    b: string\n}",
            self::pretty($type, new PrinterOptions(maxInlineShapeFields: 1)),
        );
    }

    public function testNestedShapeIsIndentedDeeper(): void
    {
        $inner = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([
            NamedFieldNode::from('x', self::named('int')),
        ]));
        $type = new NamedTypeNode(TypeName::from('array'), null, new FieldListNode([
            NamedFieldNode::from('a', $inner),
        ]));

        self::assertSame(
            "array{\n    a: array{\n        x: int\n    }\n}",
            self::pretty($type, new PrinterOptions(maxInlineShapeFields: 0)),
        );
    }
}
