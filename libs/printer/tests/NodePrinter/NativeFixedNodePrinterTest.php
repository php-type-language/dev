<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\NativeFixedNodePrinter;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

#[CoversClass(NativeFixedNodePrinter::class)]
final class NativeFixedNodePrinterTest extends NodePrinterTestCase
{
    /**
     * @return iterable<non-empty-string, array{TypeNode, non-empty-string}>
     */
    public static function nodesDataProvider(): iterable
    {
        $mask = new MaskNode([new WildcardMaskNode()]);

        yield 'this' => [new ThisTypeNode(), 'self'];
        yield 'list' => [new TypesListTypeNode(self::named('int')), 'iterable'];
        yield 'offset' => [new TypeOffsetAccessTypeNode(self::named('Foo'), self::named('K')), 'mixed'];
        yield 'class constant' => [ClassConstTypeNode::from('Some', 'FOO'), 'mixed'];
        yield 'class constant mask' => [new ClassConstMaskTypeNode(TypeName::from('Some'), $mask), 'mixed'];
        yield 'constant mask' => [new ConstMaskTypeNode($mask), 'mixed'];
    }

    #[DataProvider('nodesDataProvider')]
    public function testNodeIsPrintedAsItsNativeType(TypeNode $node, string $expected): void
    {
        self::assertSame($expected, self::native($node));
    }
}
