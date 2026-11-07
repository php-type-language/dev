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
use TypeLang\Type\Name\NamespaceName;

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
        $type = new ConstMaskTypeNode(self::mask(), NamespaceName::from('Some\Any'));

        self::assertSame('Some\Any\JSON_*', self::pretty($type));
    }

    public function testFullyQualifiedNamespaceKeepsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), NamespaceName::from('\Some\Any'));

        self::assertSame('\Some\Any\JSON_*', self::pretty($type));
    }

    public function testRootNamespaceIsWrittenAsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(self::mask(), new NamespaceName([], true));

        self::assertSame('\JSON_*', self::pretty($type));
    }
}
