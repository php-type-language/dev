<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Registry;

use TypeLang\Printer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Registry\CachedNodePrinterRegistry;
use TypeLang\Printer\NodePrinter\ThisTypeNodePrinter;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistryInterface;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\ThisTypeNode;

#[CoversClass(CachedNodePrinterRegistry::class)]
final class CachedNodePrinterRegistryTest extends TestCase
{
    public function testPrinterOfTheDelegateIsReturned(): void
    {
        $printer = new ThisTypeNodePrinter();
        $registry = new CachedNodePrinterRegistry(new NodePrinterRegistry([ThisTypeNode::class => $printer]));

        self::assertSame($printer, $registry->getPrinter(new ThisTypeNode()));
    }

    public function testDelegateIsAskedOncePerNodeClass(): void
    {
        $delegate = new class implements NodePrinterRegistryInterface {
            public int $calls = 0;

            public function getPrinter(Node $node): NodePrinterInterface
            {
                ++$this->calls;

                return new ThisTypeNodePrinter();
            }
        };

        $registry = new CachedNodePrinterRegistry($delegate);

        $registry->getPrinter(new ThisTypeNode());
        $registry->getPrinter(new ThisTypeNode());
        $registry->getPrinter(new NamedTypeNode(new Name('int')));

        self::assertSame(2, $delegate->calls);
    }
}
