<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\NativeNamedTypeNodePrinter;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;

#[CoversClass(NativeNamedTypeNodePrinter::class)]
final class NativeNamedTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testNameWithNoAliasIsPrintedAsIs(): void
    {
        self::assertSame('Some\Any', self::native(new NamedTypeNode(new Name('Some\Any'))));
    }

    public function testBuiltinAliasIsResolved(): void
    {
        self::assertSame('int', self::native(new NamedTypeNode(new Name('positive-int'))));
    }

    public function testAliasIsResolvedInAnyCase(): void
    {
        self::assertSame('int', self::native(new NamedTypeNode(new Name('Positive-INT'))));
    }

    public function testCustomAliasIsResolved(): void
    {
        self::assertSame('int', self::native(new NamedTypeNode(new Name('Custom')), ['Custom' => 'int']));
    }

    public function testTemplateArgumentsAndShapeAreDropped(): void
    {
        $arguments = new TemplateListNode([new TypedTemplateArgumentNode(self::named('int'))]);

        $generic = new NamedTypeNode(new Name('array'), $arguments);
        $shape = new NamedTypeNode(new Name('array'), null, new FieldListNode([]));

        self::assertSame('array', self::native($generic));
        self::assertSame('array', self::native($shape));
    }
}
