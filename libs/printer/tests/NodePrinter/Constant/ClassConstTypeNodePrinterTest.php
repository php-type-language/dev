<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Constant;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Constant\ClassConstTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

#[CoversClass(ClassConstTypeNodePrinter::class)]
final class ClassConstTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testClassAndConstantAreJoinedByTheDoubleColon(): void
    {
        $class = new Name('Any', new NamespaceName('Some'));

        self::assertSame('Some\Any::FOO', self::pretty(new ClassConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testFullyQualifiedClassKeepsTheLeadingSeparator(): void
    {
        $class = new Name('Some', new NamespaceName('', NamespaceKind::FullyQualified));

        self::assertSame('\Some::FOO', self::pretty(new ClassConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testClassRelativeToTheCurrentNamespaceKeepsTheKeyword(): void
    {
        $class = new Name('Some', new NamespaceName('namespace'));

        self::assertSame('namespace\Some::FOO', self::pretty(new ClassConstTypeNode($class, new Identifier('FOO'))));
    }

    public function testClassMayBeSpecial(): void
    {
        self::assertSame('self::FOO', self::pretty(new ClassConstTypeNode(new Name('self'), new Identifier('FOO'))));
    }
}
