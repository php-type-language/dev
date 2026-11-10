<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Constant;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Constant\ConstTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

#[CoversClass(ConstTypeNodePrinter::class)]
final class ConstTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testClassAndConstantAreJoinedByTheDoubleColon(): void
    {
        $class = new Name('Any', new NamespaceName('Some'));

        self::assertSame('Some\Any::FOO', self::pretty(new ConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testFullyQualifiedClassKeepsTheLeadingSeparator(): void
    {
        $class = new Name('Some', new NamespaceName('', NamespaceKind::FullyQualified));

        self::assertSame('\Some::FOO', self::pretty(new ConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testClassRelativeToTheCurrentNamespaceKeepsTheKeyword(): void
    {
        $class = new Name('Some', new NamespaceName('namespace'));

        self::assertSame('namespace\Some::FOO', self::pretty(new ConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testClassMayBeSpecial(): void
    {
        self::assertSame('self::FOO', self::pretty(new ConstTypeNode(new Name('self'), new Identifier('FOO'))));
    }

    public function testNamespaceAndConstantAreJoinedByTheSeparator(): void
    {
        $namespace = new NamespaceName('Some\Any');

        self::assertSame('Some\Any\FOO', self::pretty(new ConstTypeNode($namespace, new Identifier('FOO'))));
    }

    public function testConstantOfTheRootNamespaceIsWrittenWithTheSeparatorAlone(): void
    {
        $namespace = new NamespaceName('', NamespaceKind::FullyQualified);

        self::assertSame('\FOO', self::pretty(new ConstTypeNode($namespace, new Identifier('FOO'))));
    }
}
