<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Registry;

use TypeLang\Printer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\NodePrinter\ThisTypeNodePrinter;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Node;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;

#[CoversClass(NodePrinterRegistry::class)]
final class NodePrinterRegistryTest extends TestCase
{
    /**
     * @return NodePrinterInterface<Node>
     */
    private static function printer(): NodePrinterInterface
    {
        return new class implements NodePrinterInterface {
            public function printNode(Node $node, PrinterContext $context): string
            {
                return 'printed';
            }
        };
    }

    public function testPrinterOfTheNodeClassIsFound(): void
    {
        $printer = new ThisTypeNodePrinter();
        $registry = new NodePrinterRegistry([ThisTypeNode::class => $printer]);

        self::assertSame($printer, $registry->getPrinter(new ThisTypeNode()));
    }

    public function testPrinterOfTheParentClassIsFound(): void
    {
        $printer = self::printer();
        $registry = new NodePrinterRegistry([LiteralNode::class => $printer]);

        self::assertSame($printer, $registry->getPrinter(new IntLiteralNode(1)));
        self::assertSame($printer, $registry->getPrinter(new StringLiteralNode('a')));
    }

    public function testPrinterOfTheClosestClassWins(): void
    {
        $parent = self::printer();
        $closest = self::printer();
        $registry = new NodePrinterRegistry([
            TypeNode::class => $parent,
            LiteralNode::class => $closest,
        ]);

        self::assertSame($closest, $registry->getPrinter(new IntLiteralNode(1)));
        self::assertSame($parent, $registry->getPrinter(new ThisTypeNode()));
    }

    public function testNodeWithNoPrinterIsRejected(): void
    {
        $this->expectException(NonPrintableNodeException::class);

        (new NodePrinterRegistry())->getPrinter(new ThisTypeNode());
    }

    public function testWithReplacesThePrinterAndKeepsTheOriginalRegistry(): void
    {
        $original = new ThisTypeNodePrinter();
        $replacement = self::printer();

        $registry = new NodePrinterRegistry([ThisTypeNode::class => $original]);
        $replaced = $registry->with(ThisTypeNode::class, $replacement);

        self::assertSame($replacement, $replaced->getPrinter(new ThisTypeNode()));
        self::assertSame($original, $registry->getPrinter(new ThisTypeNode()));
    }
}
