<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Constant;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Constant\ConstMaskTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

#[CoversClass(ConstMaskTypeNodePrinter::class)]
final class ConstMaskTypeNodePrinterTest extends NodePrinterTestCase
{
    private static function mask(): MaskNode
    {
        return new MaskNode([new Identifier('JSON_'), new WildcardMaskNode()]);
    }

    public function testMaskWithNoNamespaceIsWrittenAlone(): void
    {
        self::assertSame('JSON_*', self::pretty(new ConstMaskTypeNode(self::mask())));
    }

    public function testNamespaceIsWrittenBeforeTheMask(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName('Some\Any'));

        self::assertSame('Some\Any\JSON_*', self::pretty($type));
    }

    public function testFullyQualifiedNamespaceKeepsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName('Some\Any', NamespaceKind::FullyQualified));

        self::assertSame('\Some\Any\JSON_*', self::pretty($type));
    }

    public function testRootNamespaceIsWrittenAsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName('', NamespaceKind::FullyQualified));

        self::assertSame('\JSON_*', self::pretty($type));
    }

    public function testNamespaceRelativeToTheCurrentOneKeepsTheKeyword(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName('namespace\Some'));

        self::assertSame('namespace\Some\JSON_*', self::pretty($type));
    }

    public function testCurrentNamespaceIsWrittenAsTheKeywordAlone(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName('namespace'));

        self::assertSame('namespace\JSON_*', self::pretty($type));
    }

    public function testStarAloneInTheRootNamespace(): void
    {
        $namespace = new NamespaceName('', NamespaceKind::FullyQualified);
        $type = new ConstMaskTypeNode(new MaskNode([new WildcardMaskNode()]), $namespace);

        self::assertSame('\*', self::pretty($type));
    }

    public function testStarAloneInANamespace(): void
    {
        $type = new ConstMaskTypeNode(new MaskNode([new WildcardMaskNode()]), new NamespaceName('Path\To'));

        self::assertSame('Path\To\*', self::pretty($type));
    }
}
