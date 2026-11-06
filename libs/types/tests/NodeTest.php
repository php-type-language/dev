<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Condition\NotEqualConditionNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\VariableNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Name\NamespaceName;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;

final class NodeTest extends TestCase
{
    private static function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode(TypeName::from($name));
    }

    /**
     * Each node of the package must be an implementation of the
     * {@see Node} contract.
     *
     * @return iterable<non-empty-string, array{Node}>
     */
    public static function provideNodes(): iterable
    {
        yield CallableTypeNode::class => [new CallableTypeNode(TypeName::from('callable'))];
        yield ClassConstMaskTypeNode::class => [new ClassConstMaskTypeNode(TypeName::from('Example'))];
        yield ClassConstTypeNode::class => [
            new ClassConstTypeNode(TypeName::from('Example'), new Identifier('CONST')),
        ];
        yield ConstMaskTypeNode::class => [new ConstMaskTypeNode(new MaskNode([new Identifier('CONST'), new WildcardMaskNode()]))];
        yield Identifier::class => [new Identifier('Example')];
        yield IntersectionTypeNode::class => [new IntersectionTypeNode([self::type('A'), self::type('B')])];
        yield MaskNode::class => [new MaskNode([new WildcardMaskNode()])];
        yield TypeName::class => [TypeName::from('Example')];
        yield NamespaceName::class => [NamespaceName::from('Example')];
        yield NamedTypeNode::class => [self::type()];
        yield NullableTypeNode::class => [new NullableTypeNode(self::type())];
        yield ConditionalTypeNode::class => [
            new ConditionalTypeNode(
                new EqualConditionNode(self::type('A'), self::type('B')),
                self::type('C'),
                self::type('D'),
            ),
        ];
        yield TypeOffsetAccessTypeNode::class => [new TypeOffsetAccessTypeNode(self::type(), self::type('Offset'))];
        yield TypesListTypeNode::class => [new TypesListTypeNode(self::type())];
        yield UnionTypeNode::class => [new UnionTypeNode([self::type('A'), self::type('B')])];
        yield WildcardMaskNode::class => [new WildcardMaskNode()];

        yield CallableParameterListNode::class => [new CallableParameterListNode()];
        yield CallableParameterNode::class => [new CallableParameterNode(self::type())];

        yield EqualConditionNode::class => [new EqualConditionNode(self::type('A'), self::type('B'))];
        yield NotEqualConditionNode::class => [new NotEqualConditionNode(self::type('A'), self::type('B'))];

        yield BoolLiteralNode::class => [new BoolLiteralNode(true)];
        yield FloatLiteralNode::class => [new FloatLiteralNode(0.1)];
        yield IntLiteralNode::class => [new IntLiteralNode(42)];
        yield NullLiteralNode::class => [new NullLiteralNode()];
        yield StringLiteralNode::class => [new StringLiteralNode('example')];
        yield ThisTypeNode::class => [new ThisTypeNode()];
        yield VariableNode::class => [new VariableNode(new Identifier('example'))];

        yield ComplexFieldNode::class => [
            new ComplexFieldNode(
                new ClassConstTypeNode(TypeName::from('Example'), new Identifier('CONST')),
                self::type(),
            ),
        ];
        yield FieldListNode::class => [new FieldListNode()];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(self::type())];
        yield NamedFieldNode::class => [new NamedFieldNode(new Identifier('key'), self::type())];
        yield ScalarFieldNode::class => [new ScalarFieldNode(new IntLiteralNode(0), self::type())];

        yield TemplateListNode::class => [
            new TemplateListNode([new TypedTemplateArgumentNode(self::type())]),
        ];
        yield TypedTemplateArgumentNode::class => [new TypedTemplateArgumentNode(self::type())];
        yield WildcardTemplateArgumentNode::class => [new WildcardTemplateArgumentNode()];
        yield TemplateBoundNode::class => [new TemplateBoundNode(new Identifier('of'), self::type())];
        yield TemplateParameterListNode::class => [
            new TemplateParameterListNode([new TemplateParameterNode(new Identifier('T'))]),
        ];
        yield TemplateParameterNode::class => [new TemplateParameterNode(new Identifier('T'))];
    }

    #[Test]
    #[DataProvider('provideNodes')]
    public function everyNodeHasZeroOffsetByDefault(Node $node): void
    {
        self::assertSame(0, $node->offset);
        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function abstractNodeOffsetDefaultsToZero(): void
    {
        $node = new class extends Node {};

        self::assertSame(0, $node->offset);
    }

    /**
     * Every node accepts the source code offset it has been read at as the
     * last argument of its constructor.
     *
     * @return iterable<non-empty-string, array{Node}>
     */
    public static function provideNodesWithOffset(): iterable
    {
        yield CallableTypeNode::class => [
            new CallableTypeNode(TypeName::from('callable'), offset: 42),
        ];
        yield ClassConstMaskTypeNode::class => [
            new ClassConstMaskTypeNode(TypeName::from('Example'), offset: 42),
        ];
        yield ClassConstTypeNode::class => [
            new ClassConstTypeNode(TypeName::from('Example'), new Identifier('CONST'), 42),
        ];
        yield ConstMaskTypeNode::class => [
            new ConstMaskTypeNode(new MaskNode([new Identifier('CONST'), new WildcardMaskNode()]), offset: 42),
        ];
        yield Identifier::class => [new Identifier('Example', 42)];
        yield MaskNode::class => [new MaskNode([new WildcardMaskNode()], 42)];
        yield TypeName::class => [TypeName::from('Example', 42)];
        yield NamespaceName::class => [NamespaceName::from('Example', 42)];
        yield NamedTypeNode::class => [new NamedTypeNode(TypeName::from('Example'), offset: 42)];
        yield NullableTypeNode::class => [new NullableTypeNode(self::type(), 42)];
        yield ConditionalTypeNode::class => [
            new ConditionalTypeNode(
                new EqualConditionNode(self::type('A'), self::type('B')),
                self::type('C'),
                self::type('D'),
                42,
            ),
        ];
        yield TypeOffsetAccessTypeNode::class => [
            new TypeOffsetAccessTypeNode(self::type(), self::type('Offset'), 42),
        ];
        yield TypesListTypeNode::class => [new TypesListTypeNode(self::type(), 42)];
        yield WildcardMaskNode::class => [new WildcardMaskNode(42)];

        yield CallableParameterListNode::class => [new CallableParameterListNode([], 42)];
        yield CallableParameterNode::class => [new CallableParameterNode(self::type(), offset: 42)];

        yield EqualConditionNode::class => [new EqualConditionNode(self::type('A'), self::type('B'), 42)];
        yield NotEqualConditionNode::class => [
            new NotEqualConditionNode(self::type('A'), self::type('B'), 42),
        ];

        yield BoolLiteralNode::class => [new BoolLiteralNode(true, offset: 42)];
        yield FloatLiteralNode::class => [new FloatLiteralNode(0.1, offset: 42)];
        yield IntLiteralNode::class => [new IntLiteralNode(42, offset: 42)];
        yield NullLiteralNode::class => [new NullLiteralNode(offset: 42)];
        yield StringLiteralNode::class => [new StringLiteralNode('example', offset: 42)];
        yield ThisTypeNode::class => [new ThisTypeNode(42)];
        yield VariableNode::class => [new VariableNode(new Identifier('example'), 42)];

        yield ComplexFieldNode::class => [
            new ComplexFieldNode(
                new ClassConstTypeNode(TypeName::from('Example'), new Identifier('CONST')),
                self::type(),
                offset: 42,
            ),
        ];
        yield FieldListNode::class => [new FieldListNode(offset: 42)];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(self::type(), offset: 42)];
        yield NamedFieldNode::class => [
            new NamedFieldNode(new Identifier('key'), self::type(), offset: 42),
        ];
        yield ScalarFieldNode::class => [
            new ScalarFieldNode(new IntLiteralNode(0), self::type(), offset: 42),
        ];

        yield TemplateListNode::class => [
            new TemplateListNode([new TypedTemplateArgumentNode(self::type())], 42),
        ];
        yield TypedTemplateArgumentNode::class => [new TypedTemplateArgumentNode(self::type(), offset: 42)];
        yield WildcardTemplateArgumentNode::class => [new WildcardTemplateArgumentNode(42)];
        yield TemplateBoundNode::class => [
            new TemplateBoundNode(new Identifier('of'), self::type(), 42),
        ];
        yield TemplateParameterListNode::class => [
            new TemplateParameterListNode([new TemplateParameterNode(new Identifier('T'))], 42),
        ];
        yield TemplateParameterNode::class => [
            new TemplateParameterNode(new Identifier('T'), offset: 42),
        ];

        yield UnionTypeNode::class => [new UnionTypeNode([self::type('A'), self::type('B')], 42)];
        yield IntersectionTypeNode::class => [
            new IntersectionTypeNode([self::type('A'), self::type('B')], 42),
        ];
    }

    #[Test]
    #[DataProvider('provideNodesWithOffset')]
    public function everyNodeConstructorPassesTheOffsetThrough(Node $node): void
    {
        self::assertSame(42, $node->offset);
    }

    /**
     * Guards the provider above from getting out of sync with the package:
     * every node takes the offset as the last argument of its constructor and
     * must be listed there.
     */
    #[Test]
    public function everyNodeIsCoveredByTheOffsetProvider(): void
    {
        $expected = $actual = [];

        foreach (self::provideNodes() as [$node]) {
            $parameters = (new \ReflectionObject($node))
                ->getConstructor()?->getParameters() ?? [];

            $last = \end($parameters);

            self::assertNotFalse($last, $node::class . ' must take an offset');
            self::assertSame('offset', $last->getName(), $node::class . ' must take an offset last');

            $expected[] = $node::class;
        }

        foreach (self::provideNodesWithOffset() as [$node]) {
            $actual[] = $node::class;
        }

        \sort($expected);
        \sort($actual);

        self::assertSame($expected, $actual);
    }
}
