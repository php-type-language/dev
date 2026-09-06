<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeArgumentListNode;
use TypeLang\Type\Attribute\AttributeArgumentNode;
use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Attribute\AttributeGroupNode;
use TypeLang\Type\Attribute\AttributeNode;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Condition\GreaterThanConditionNode;
use TypeLang\Type\Condition\GreaterThanOrEqualConditionNode;
use TypeLang\Type\Condition\LessThanConditionNode;
use TypeLang\Type\Condition\LessThanOrEqualConditionNode;
use TypeLang\Type\Condition\NotEqualConditionNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\NodeInterface;
use TypeLang\Type\NullableTypeNode;
use TypeLang\Type\Shape\ClassConstFieldNode;
use TypeLang\Type\Shape\ClassConstMaskFieldNode;
use TypeLang\Type\Shape\ConstMaskFieldNode;
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\NumericFieldNode;
use TypeLang\Type\Shape\StringNamedFieldNode;
use TypeLang\Type\Template\TemplateArgumentListNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\TernaryExpressionNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessNode;
use TypeLang\Type\TypesListNode;
use TypeLang\Type\UnionTypeNode;

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
        yield ConstMaskNode::class => [new ConstMaskNode(Name::createFromString('CONST'))];
        yield Identifier::class => [new Identifier('Example')];
        yield IntersectionTypeNode::class => [new IntersectionTypeNode(self::type('A'), self::type('B'))];
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
        yield UnionTypeNode::class => [new UnionTypeNode(self::type('A'), self::type('B'))];

        yield AttributeArgumentListNode::class => [new AttributeArgumentListNode()];
        yield AttributeArgumentNode::class => [new AttributeArgumentNode(self::type())];
        yield AttributeGroupListNode::class => [new AttributeGroupListNode()];
        yield AttributeGroupNode::class => [new AttributeGroupNode()];
        yield AttributeNode::class => [new AttributeNode(Name::createFromString('Deprecated'))];

        yield CallableParameterListNode::class => [new CallableParameterListNode()];
        yield CallableParameterNode::class => [new CallableParameterNode(self::type())];

        yield EqualConditionNode::class => [new EqualConditionNode(self::type('A'), self::type('B'))];
        yield GreaterThanConditionNode::class => [new GreaterThanConditionNode(self::type('A'), self::type('B'))];
        yield GreaterThanOrEqualConditionNode::class => [
            new GreaterThanOrEqualConditionNode(self::type('A'), self::type('B')),
        ];
        yield LessThanConditionNode::class => [new LessThanConditionNode(self::type('A'), self::type('B'))];
        yield LessThanOrEqualConditionNode::class => [
            new LessThanOrEqualConditionNode(self::type('A'), self::type('B')),
        ];
        yield NotEqualConditionNode::class => [new NotEqualConditionNode(self::type('A'), self::type('B'))];

        yield BoolLiteralNode::class => [new BoolLiteralNode(true)];
        yield FloatLiteralNode::class => [new FloatLiteralNode(0.1)];
        yield IntLiteralNode::class => [new IntLiteralNode(42)];
        yield NullLiteralNode::class => [new NullLiteralNode()];
        yield StringLiteralNode::class => [new StringLiteralNode('example')];
        yield VariableLiteralNode::class => [new VariableLiteralNode('example')];

        yield ClassConstFieldNode::class => [
            new ClassConstFieldNode(
                new ClassConstNode(Name::createFromString('Example'), new Identifier('CONST')),
                self::type(),
            ),
        ];
        yield ClassConstMaskFieldNode::class => [
            new ClassConstMaskFieldNode(new ClassConstMaskNode(Name::createFromString('Example')), self::type()),
        ];
        yield ConstMaskFieldNode::class => [
            new ConstMaskFieldNode(new ConstMaskNode(Name::createFromString('CONST')), self::type()),
        ];
        yield FieldsListNode::class => [new FieldsListNode()];
        yield ImplicitFieldNode::class => [new ImplicitFieldNode(self::type())];
        yield NamedFieldNode::class => [new NamedFieldNode(new Identifier('key'), self::type())];
        yield NumericFieldNode::class => [new NumericFieldNode(new IntLiteralNode(0), self::type())];
        yield StringNamedFieldNode::class => [
            new StringNamedFieldNode(new StringLiteralNode('key'), self::type()),
        ];

        yield TemplateArgumentListNode::class => [new TemplateArgumentListNode()];
        yield TemplateArgumentNode::class => [new TemplateArgumentNode(self::type())];
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
}
