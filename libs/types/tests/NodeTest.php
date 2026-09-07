<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Condition\NotEqualConditionNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\ThisNode;
use TypeLang\Type\VariableNode;
use TypeLang\Type\MaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\NodeInterface;
use TypeLang\Type\NullableTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Template\TemplateArgumentListNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Template\TemplateBoundEdgeNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\TernaryExpressionNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessNode;
use TypeLang\Type\TypesListNode;
use TypeLang\Type\UnionTypeNode;
use TypeLang\Type\WildcardNode;

final class NodeTest extends TestCase
{
    private static function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    /**
     * Each node of the package must be an implementation of the
     * {@see NodeInterface} contract.
     *
     * @return iterable<non-empty-string, array{Node}>
     */
    public static function provideNodes(): iterable
    {
        yield CallableTypeNode::class => [new CallableTypeNode(Name::createFromString('callable'))];
        yield ClassConstMaskNode::class => [new ClassConstMaskNode(Name::createFromString('Example'))];
        yield ClassConstNode::class => [
            new ClassConstNode(Name::createFromString('Example'), new Identifier('CONST')),
        ];
        yield ConstMaskNode::class => [new ConstMaskNode(new MaskNode([new Identifier('CONST'), new WildcardNode()]))];
        yield Identifier::class => [new Identifier('Example')];
        yield IntersectionTypeNode::class => [new IntersectionTypeNode([self::type('A'), self::type('B')])];
        yield MaskNode::class => [new MaskNode([new WildcardNode()])];
        yield Name::class => [Name::createFromString('Example')];
        yield NamedTypeNode::class => [self::type()];
        yield NullableTypeNode::class => [new NullableTypeNode(self::type())];
        yield TernaryExpressionNode::class => [
            new TernaryExpressionNode(
                new EqualConditionNode(self::type('A'), self::type('B')),
                self::type('C'),
                self::type('D'),
            ),
        ];
        yield TypeOffsetAccessNode::class => [new TypeOffsetAccessNode(self::type(), self::type('Offset'))];
        yield TypesListNode::class => [new TypesListNode(self::type())];
        yield UnionTypeNode::class => [new UnionTypeNode([self::type('A'), self::type('B')])];
        yield WildcardNode::class => [new WildcardNode()];

        yield CallableParameterListNode::class => [new CallableParameterListNode()];
        yield CallableParameterNode::class => [new CallableParameterNode(self::type())];

        yield EqualConditionNode::class => [new EqualConditionNode(self::type('A'), self::type('B'))];
        yield NotEqualConditionNode::class => [new NotEqualConditionNode(self::type('A'), self::type('B'))];

        yield BoolLiteralNode::class => [new BoolLiteralNode(true)];
        yield FloatLiteralNode::class => [new FloatLiteralNode(0.1)];
        yield IntLiteralNode::class => [new IntLiteralNode(42)];
        yield NullLiteralNode::class => [new NullLiteralNode()];
        yield StringLiteralNode::class => [new StringLiteralNode('example')];
        yield ThisNode::class => [new ThisNode()];
        yield VariableNode::class => [new VariableNode(new Identifier('example'))];

        yield ComplexFieldNode::class => [
            new ComplexFieldNode(
                new ClassConstNode(Name::createFromString('Example'), new Identifier('CONST')),
                self::type(),
            ),
        ];
        yield FieldsListNode::class => [new FieldsListNode()];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(self::type())];
        yield NamedFieldNode::class => [new NamedFieldNode(new Identifier('key'), self::type())];
        yield ScalarFieldNode::class => [new ScalarFieldNode(new IntLiteralNode(0), self::type())];

        yield TemplateArgumentListNode::class => [
            new TemplateArgumentListNode([new TemplateArgumentNode(self::type())]),
        ];
        yield TemplateArgumentNode::class => [new TemplateArgumentNode(self::type())];
        yield TemplateBoundEdgeNode::class => [new TemplateBoundEdgeNode(new Identifier('of'), self::type())];
        yield TemplateParameterListNode::class => [new TemplateParameterListNode()];
        yield TemplateParameterNode::class => [new TemplateParameterNode(new Identifier('T'))];
    }

    #[Test]
    #[DataProvider('provideNodes')]
    public function everyNodeImplementsNodeInterface(Node $node): void
    {
        self::assertInstanceOf(NodeInterface::class, $node);
    }

    #[Test]
    #[DataProvider('provideNodes')]
    public function everyNodeHasZeroOffsetByDefault(Node $node): void
    {
        self::assertSame(0, $node->offset);
        self::assertSame(0, $node->offset);
    }

    #[Test]
    #[DataProvider('provideNodes')]
    public function everyNodeOffsetIsWritableAndReflectedByMethod(Node $node): void
    {
        $node->offset = 42;

        self::assertSame(42, $node->offset);
        self::assertSame(42, $node->offset, 'The offset() method must be an alias of the $offset property');
    }

    /**
     * Guards the data provider above from getting out of sync with the package:
     * every concrete node class must be listed there.
     */
    #[Test]
    public function providerCoversEveryConcreteNodeClass(): void
    {
        $covered = [];

        foreach (self::provideNodes() as [$node]) {
            $covered[] = $node::class;
        }

        $expected = [];
        $directory = \dirname(__DIR__) . '/src';
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = \substr($file->getPathname(), \strlen($directory) + 1, -4);
            $class = 'TypeLang\\Type\\' . \str_replace(['/', '\\'], '\\', $relative);

            if (!\class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            if ($reflection->isAbstract() || !$reflection->isSubclassOf(Node::class)) {
                continue;
            }

            $expected[] = $class;
        }

        \sort($expected);
        \sort($covered);

        self::assertSame($expected, $covered);
    }

    #[Test]
    public function abstractNodeOffsetDefaultsToZero(): void
    {
        $node = new class extends Node {};

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function typeNodeIsANode(): void
    {
        $node = new class extends TypeNode {};

        self::assertInstanceOf(Node::class, $node);
        self::assertInstanceOf(NodeInterface::class, $node);
    }

    #[Test]
    public function wrappingTypeNodeStoresWrappedType(): void
    {
        $inner = self::type();
        $node = new TypesListNode($inner);

        self::assertSame($inner, $node->type);
    }

    #[Test]
    public function wrappingTypeNodeTypeIsMutable(): void
    {
        $node = new TypesListNode(self::type('A'));
        $node->type = self::type('B');

        self::assertSame('B', $node->type->name->toString());
    }

    #[Test]
    #[DataProvider('provideNodes')]
    public function everyNodeOffsetIsReadableByMethod(Node $node): void
    {
        self::assertSame(0, $node->getOffset());

        $node->offset = 42;

        self::assertSame(42, $node->getOffset());
    }

    #[Test]
    public function abstractNodeOffsetIsReadableByMethod(): void
    {
        $node = new class extends Node {};

        self::assertSame(0, $node->getOffset());
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
            new CallableTypeNode(Name::createFromString('callable'), offset: 42),
        ];
        yield ClassConstMaskNode::class => [
            new ClassConstMaskNode(Name::createFromString('Example'), offset: 42),
        ];
        yield ClassConstNode::class => [
            new ClassConstNode(Name::createFromString('Example'), new Identifier('CONST'), 42),
        ];
        yield ConstMaskNode::class => [
            new ConstMaskNode(new MaskNode([new Identifier('CONST'), new WildcardNode()]), offset: 42),
        ];
        yield Identifier::class => [new Identifier('Example', 42)];
        yield MaskNode::class => [new MaskNode([new WildcardNode()], 42)];
        yield Name::class => [Name::createFromString('Example', 42)];
        yield NamedTypeNode::class => [new NamedTypeNode(Name::createFromString('Example'), offset: 42)];
        yield NullableTypeNode::class => [new NullableTypeNode(self::type(), 42)];
        yield TernaryExpressionNode::class => [
            new TernaryExpressionNode(
                new EqualConditionNode(self::type('A'), self::type('B')),
                self::type('C'),
                self::type('D'),
                42,
            ),
        ];
        yield TypeOffsetAccessNode::class => [
            new TypeOffsetAccessNode(self::type(), self::type('Offset'), 42),
        ];
        yield TypesListNode::class => [new TypesListNode(self::type(), 42)];
        yield WildcardNode::class => [new WildcardNode(42)];

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
        yield ThisNode::class => [new ThisNode(42)];
        yield VariableNode::class => [new VariableNode(new Identifier('example'), 42)];

        yield ComplexFieldNode::class => [
            new ComplexFieldNode(
                new ClassConstNode(Name::createFromString('Example'), new Identifier('CONST')),
                self::type(),
                offset: 42,
            ),
        ];
        yield FieldsListNode::class => [new FieldsListNode(offset: 42)];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(self::type(), offset: 42)];
        yield NamedFieldNode::class => [
            new NamedFieldNode(new Identifier('key'), self::type(), offset: 42),
        ];
        yield ScalarFieldNode::class => [
            new ScalarFieldNode(new IntLiteralNode(0), self::type(), offset: 42),
        ];

        yield TemplateArgumentListNode::class => [
            new TemplateArgumentListNode([new TemplateArgumentNode(self::type())], 42),
        ];
        yield TemplateArgumentNode::class => [new TemplateArgumentNode(self::type(), offset: 42)];
        yield TemplateBoundEdgeNode::class => [
            new TemplateBoundEdgeNode(new Identifier('of'), self::type(), 42),
        ];
        yield TemplateParameterListNode::class => [new TemplateParameterListNode([], 42)];
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
        self::assertSame(42, $node->getOffset());
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
