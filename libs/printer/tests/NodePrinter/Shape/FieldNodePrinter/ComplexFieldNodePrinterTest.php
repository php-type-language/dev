<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Shape\FieldNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ComplexFieldNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;

#[CoversClass(ComplexFieldNodePrinter::class)]
final class ComplexFieldNodePrinterTest extends NodePrinterTestCase
{
    public function testKeyOfATypeIsPrintedAsTheType(): void
    {
        $field = new ComplexFieldNode(self::named('User'), self::named('T'));

        self::assertSame('User: T', self::pretty($field));
    }

    public function testKeyOfAConstantIsPrintedAsTheConstant(): void
    {
        $field = new ComplexFieldNode(ClassConstTypeNode::from('Some', 'FOO'), self::named('int'), true);

        self::assertSame('Some::FOO?: int', self::pretty($field));
    }

    public function testUnionKeyNeedsNoBrackets(): void
    {
        $field = new ComplexFieldNode(self::union(self::named('int'), self::named('string')), self::named('T'));

        self::assertSame('int|string: T', self::pretty($field));
    }
}
