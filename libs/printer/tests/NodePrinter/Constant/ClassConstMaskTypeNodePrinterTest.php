<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Constant;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Constant\ClassConstMaskTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;

#[CoversClass(ClassConstMaskTypeNodePrinter::class)]
final class ClassConstMaskTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testClassAndMaskAreJoinedByTheDoubleColon(): void
    {
        $mask = new MaskNode([new Identifier('FOO_'), new WildcardMaskNode()]);

        $type = new ClassConstMaskTypeNode(new Name('Any', new NamespaceName('Some')), $mask);

        self::assertSame('Some\Any::FOO_*', self::pretty($type));
    }

    public function testDefaultMaskIsTheWildcard(): void
    {
        self::assertSame('Some::*', self::pretty(new ClassConstMaskTypeNode(new Name('Some'))));
    }

    public function testMaskMayHaveSeveralWildcards(): void
    {
        $mask = new MaskNode([
            new Identifier('A'),
            new WildcardMaskNode(),
            new Identifier('B'),
            new WildcardMaskNode(),
        ]);

        self::assertSame('Some::A*B*', self::pretty(new ClassConstMaskTypeNode(new Name('Some'), $mask)));
    }
}
