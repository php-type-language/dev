<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\PrinterOptions\UnionSpacing;
use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\UnionTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;

#[CoversClass(UnionTypeNodePrinter::class)]
final class UnionTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testTypesAreJoinedByThePipe(): void
    {
        $type = self::union(self::named('int'), self::named('string'), self::named('null'));

        self::assertSame('int|string|null', self::pretty($type));
    }

    public function testPipeIsWrappedByWhitespaces(): void
    {
        $type = self::union(self::named('int'), self::named('string'));

        self::assertSame('int | string', self::pretty($type, new PrinterOptions(unionSpacing: UnionSpacing::Spaced)));
    }

    public function testRepeatedTypesAreWrittenOnce(): void
    {
        $type = self::union(self::named('int'), self::named('string'), self::named('int'));

        self::assertSame('int|string', self::pretty($type));
    }

    public function testUnionOfOneTypeAfterDeduplicationIsWrittenAsTheType(): void
    {
        self::assertSame('int', self::pretty(self::union(self::named('int'), self::named('int'))));
    }

    public function testIntersectionInsideIsWrapped(): void
    {
        $type = self::union(
            self::named('A'),
            new IntersectionTypeNode([self::named('B'), self::named('C')]),
        );

        self::assertSame('A|(B & C)', self::pretty($type));
    }

    public function testUnionInsideAnIntersectionIsWrapped(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::union(self::named('B'), self::named('C'))]);

        self::assertSame('A & (B|C)', self::pretty($type));
    }

    public function testUnionInsideAnIntersectionOfOneTypeIsNotWrapped(): void
    {
        $type = new IntersectionTypeNode([self::named('A'), self::union(self::named('B'), self::named('B'))]);

        self::assertSame('A & B', self::pretty($type));
    }

    public function testEveryUnionOfTemplateArgumentsIsWrittenWithNoBrackets(): void
    {
        $type = new NamedTypeNode(new Name('array'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::union(self::named('int'), self::named('string'))),
            new TypedTemplateArgumentNode(self::union(self::named('float'), self::named('bool'))),
        ]));

        self::assertSame('array<int|string, float|bool>', self::pretty($type));
    }

    public function testTemplateArgumentsOfATypeInsideAnIntersectionAreNotWrapped(): void
    {
        $generic = new NamedTypeNode(new Name('Foo'), new TemplateListNode([
            new TypedTemplateArgumentNode(self::union(self::named('A'), self::named('B'))),
        ]));

        $options = new PrinterOptions(intersectionSpacing: IntersectionSpacing::Compact);

        self::assertSame('Foo<A|B>&C', self::pretty(new IntersectionTypeNode([$generic, self::named('C')]), $options));
    }

    public function testNestingIsNotKeptBetweenSeparatePrints(): void
    {
        $type = self::union(self::named('int'), self::named('string'));

        self::assertSame('int|string', self::pretty($type));
        self::assertSame('int|string', self::pretty($type));
    }

    public function testUnionAddedToAUnionAfterwardsIsFlattened(): void
    {
        $type = self::union(self::named('A'), self::named('B'));
        $type->children[] = self::union(self::named('C'), self::named('D'));

        self::assertSame('A|B|C|D', self::pretty($type));
    }

    public function testNestedUnionOfTheSameKindIsFlattened(): void
    {
        $type = new UnionTypeNode([
            self::named('A'),
            self::union(self::named('B'), self::named('C')),
        ]);

        self::assertSame('A|B|C', self::pretty($type));
    }
}
