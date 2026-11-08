<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Callable;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Callable\NativeCallableTypeNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Name;

#[CoversClass(NativeCallableTypeNodePrinter::class)]
final class NativeCallableTypeNodePrinterTest extends NodePrinterTestCase
{
    public function testParametersAndReturnTypeAreDropped(): void
    {
        $type = new CallableTypeNode(
            new Name('callable'),
            new CallableParameterListNode([new CallableParameterNode(self::named('int'))]),
            self::named('void'),
        );

        self::assertSame('callable', self::native($type));
    }

    public function testNameIsResolvedByTheAliases(): void
    {
        $type = new CallableTypeNode(new Name('pure-callable'));

        self::assertSame('callable', self::native($type));
    }

    public function testCustomAliasIsResolved(): void
    {
        $type = new CallableTypeNode(new Name('Handler'));

        self::assertSame('Closure', self::native($type, ['Handler' => 'Closure']));
    }
}
