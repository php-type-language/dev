<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Constant;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Constant\ClassConstTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Name;

#[CoversClass(ClassConstTypeNodePrinter::class)]
final class ClassConstTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testClassAndConstantAreJoinedByTheDoubleColon(): void
    {
        self::assertSame('Some\Any::FOO', self::pretty(new ClassConstTypeNode(new Name('Some\Any'), new Name('FOO'))));
    }

    public function testFullyQualifiedClassKeepsTheLeadingSeparator(): void
    {
        self::assertSame('\Some::FOO', self::pretty(new ClassConstTypeNode(new Name('\Some'), new Name('FOO'))));
    }

    public function testClassMayBeSpecial(): void
    {
        self::assertSame('self::FOO', self::pretty(new ClassConstTypeNode(new Name('self'), new Name('FOO'))));
    }
}
