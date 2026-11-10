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
use TypeLang\Type\Name;
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
        self::assertSame('JSON_*', self::pretty(new ConstMaskTypeNode(null, self::mask())));
    }

    public function testNamespaceIsWrittenBeforeTheMask(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('Some\Any'), self::mask());

        self::assertSame('Some\Any\JSON_*', self::pretty($type));
    }

    public function testFullyQualifiedNamespaceKeepsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('Some\Any', NamespaceKind::FullyQualified), self::mask());

        self::assertSame('\Some\Any\JSON_*', self::pretty($type));
    }

    public function testRootNamespaceIsWrittenAsTheLeadingSeparator(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('', NamespaceKind::FullyQualified), self::mask());

        self::assertSame('\JSON_*', self::pretty($type));
    }

    public function testNamespaceRelativeToTheCurrentOneKeepsTheKeyword(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('namespace\Some'), self::mask());

        self::assertSame('namespace\Some\JSON_*', self::pretty($type));
    }

    public function testCurrentNamespaceIsWrittenAsTheKeywordAlone(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('namespace'), self::mask());

        self::assertSame('namespace\JSON_*', self::pretty($type));
    }

    public function testStarAloneInTheRootNamespace(): void
    {
        $namespace = new NamespaceName('', NamespaceKind::FullyQualified);
        $type = new ConstMaskTypeNode($namespace, new MaskNode([new WildcardMaskNode()]));

        self::assertSame('\*', self::pretty($type));
    }

    public function testStarAloneInANamespace(): void
    {
        $type = new ConstMaskTypeNode(new NamespaceName('Path\To'), new MaskNode([new WildcardMaskNode()]));

        self::assertSame('Path\To\*', self::pretty($type));
    }
    public function testClassAndMaskAreJoinedByTheDoubleColon(): void
    {
        $mask = new MaskNode([new Identifier('FOO_'), new WildcardMaskNode()]);

        $type = new ConstMaskTypeNode(new Name('Any', new NamespaceName('Some')), $mask);

        self::assertSame('Some\Any::FOO_*', self::pretty($type));
    }

    public function testStarAloneInAClass(): void
    {
        $type = new ConstMaskTypeNode(new Name('Some'), new MaskNode([new WildcardMaskNode()]));

        self::assertSame('Some::*', self::pretty($type));
    }

    public function testMaskOfAClassMayHaveSeveralWildcards(): void
    {
        $mask = new MaskNode([
            new Identifier('A'),
            new WildcardMaskNode(),
            new Identifier('B'),
            new WildcardMaskNode(),
        ]);

        self::assertSame('Some::A*B*', self::pretty(new ConstMaskTypeNode(new Name('Some'), $mask)));
    }
}
