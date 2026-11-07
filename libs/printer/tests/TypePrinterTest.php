<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\TypePrinter;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;

#[CoversClass(TypePrinter::class)]
final class TypePrinterTest extends TestCase
{
    public function testPrinterOfANodeIsReplacedWithoutSubclassing(): void
    {
        /** @var NodePrinterInterface<NamedTypeNode> $shouting */
        $shouting = new class implements NodePrinterInterface {
            public function printNode(Node $node, PrinterContext $context): string
            {
                return \strtoupper($node->name->toString());
            }
        };

        $printer = new TypePrinter(PrettyTypePrinter::createRegistry()->with(NamedTypeNode::class, $shouting));

        self::assertSame('INT|STRING', $printer->print(new UnionTypeNode([
            NamedTypeNode::from('int'),
            NamedTypeNode::from('string'),
        ])));
    }
}
