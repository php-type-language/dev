<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Callable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\Callable\CallableParameterNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\DefaultValueNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\VariableNode;

#[CoversClass(CallableParameterNodePrinter::class)]
final class CallableParameterNodePrinterTest extends NodePrinterTestCase
{
    /**
     * @return iterable<non-empty-string, array{CallableParameterNode, non-empty-string}>
     */
    public static function parametersDataProvider(): iterable
    {
        $int = self::named('int');
        $name = new VariableNode(new Name('value'));

        yield 'type only' => [new CallableParameterNode($int), 'int'];
        yield 'type and name' => [new CallableParameterNode($int, $name), 'int $value'];
        yield 'name only' => [new CallableParameterNode(null, $name), 'mixed $value'];
        yield 'by reference' => [new CallableParameterNode($int, $name, true), 'int &$value'];
        yield 'variadic' => [new CallableParameterNode($int, $name, false, true), 'int ...$value'];
        yield 'optional' => [new CallableParameterNode($int, $name, false, false, new DefaultValueNode()), 'int $value='];
        yield 'by reference and variadic' => [new CallableParameterNode($int, $name, true, true), 'int &...$value'];
        yield 'variadic with no name' => [new CallableParameterNode($int, null, false, true), 'int...'];
        yield 'optional with no name' => [new CallableParameterNode($int, null, false, false, new DefaultValueNode()), 'int='];
        yield 'by reference with no name' => [new CallableParameterNode($int, null, true), 'int&'];
        yield 'with a default' => [
            new CallableParameterNode($int, $name, false, false, new DefaultValueNode(new IntLiteralNode(42))),
            'int $value = 42',
        ];
        yield 'with a default and no name' => [
            new CallableParameterNode($int, null, false, false, new DefaultValueNode(new IntLiteralNode(42))),
            'int = 42',
        ];
    }

    #[DataProvider('parametersDataProvider')]
    public function testParameterIsPrintedWithItsModifiers(CallableParameterNode $parameter, string $expected): void
    {
        self::assertSame($expected, self::pretty($parameter));
    }
}
