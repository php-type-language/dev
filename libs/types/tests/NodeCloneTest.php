<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterListNode;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Callable\DefaultValueNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Node;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\VariableNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * A clone of a node is a tree of its own: every node the original is made
 * of is cloned along with it.
 */
final class NodeCloneTest extends TestCase
{
    /**
     * @param non-empty-string $name
     */
    private static function type(string $name): NamedTypeNode
    {
        return new NamedTypeNode(new Name($name));
    }

    /**
     * Returns a tree that has a node of every class of the package in it.
     */
    private static function tree(): UnionTypeNode
    {
        $parameter = new TemplateParameterNode(
            name: new Identifier('T'),
            upper: new TemplateBoundNode(self::type('Upper'), new Identifier('of')),
            lower: new TemplateBoundNode(self::type('Lower'), new Identifier('super')),
            default: self::type('Default'),
            variance: new VarianceNode(VarianceKind::Contravariant),
        );

        $generic = new NamedTypeNode(
            name: new Name('Any', new NamespaceName('Some', NamespaceKind::FullyQualified)),
            arguments: new TemplateListNode([
                new TypedTemplateArgumentNode(self::type('int'), new VarianceNode(VarianceKind::Covariant)),
                new WildcardTemplateArgumentNode(),
                $parameter,
            ]),
        );

        $shape = new NamedTypeNode(
            name: new Name('array'),
            fields: new FieldListNode([
                new NamedFieldNode(new Identifier('name'), self::type('string'), true),
                new ScalarFieldNode(new IntLiteralNode(0), new BoolLiteralNode(true)),
                new ScalarFieldNode(new StringLiteralNode('key'), new NullLiteralNode()),
                new ComplexFieldNode(
                    new ClassConstTypeNode(new Name('Some'), new Identifier('KEY')),
                    new FloatLiteralNode(1.5),
                ),
                new ImplicitFieldNode(new ThisTypeNode()),
            ], false),
        );

        $callable = new CallableTypeNode(
            name: new Name('callable'),
            parameters: new CallableParameterListNode([
                new CallableParameterNode(
                    type: self::type('int'),
                    name: new VariableNode('value'),
                    default: new DefaultValueNode(new IntLiteralNode(42)),
                ),
                new CallableParameterNode(name: new VariableNode('rest'), isVariadic: true),
            ]),
            type: new NullableTypeNode(
                new TypesListTypeNode(
                    new TypeOffsetAccessTypeNode(self::type('T'), self::type('K')),
                ),
            ),
            templates: new TemplateParameterListNode([new TemplateParameterNode(new Identifier('U'))]),
        );

        $condition = new ConditionalTypeNode(
            condition: new ConditionNode(new VariableNode('value'), self::type('int'), ConditionKind::Equal),
            then: new ConditionalTypeNode(
                condition: new ConditionNode(self::type('A'), self::type('B'), ConditionKind::NotEqual),
                then: self::type('C'),
                else: self::type('D'),
            ),
            else: new IntersectionTypeNode([self::type('E'), self::type('F')]),
        );

        $mask = new MaskNode([new Identifier('SOME_'), new WildcardMaskNode()]);

        return new UnionTypeNode([
            $generic,
            $shape,
            $callable,
            $condition,
            new ClassConstMaskTypeNode(new Name('Some'), $mask),
            new ConstMaskTypeNode(
                new MaskNode([new WildcardMaskNode(), new Identifier('_SOME')]),
                new NamespaceName('Some'),
            ),
        ]);
    }

    /**
     * Returns the nodes of the tree that stand in the properties that can
     * be written, indexed by the identifiers of the objects.
     *
     * @return array<int, Node>
     */
    private static function collect(mixed $value): array
    {
        if (\is_array($value)) {
            $result = [];

            foreach ($value as $item) {
                $result += self::collect($item);
            }

            return $result;
        }

        if (!$value instanceof Node) {
            return [];
        }

        $result = [\spl_object_id($value) => $value];

        foreach ((new \ReflectionObject($value))->getProperties() as $property) {
            if ($property->isStatic() || $property->isReadOnly()) {
                continue;
            }

            $result += self::collect($property->getValue($value));
        }

        return $result;
    }

    /**
     * Returns the classes of the nodes the package declares.
     *
     * @return list<class-string<Node>>
     */
    private static function getNodeClasses(): array
    {
        $directory = \dirname(__DIR__) . '/src';
        $result = [];

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS));

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $relative = \substr($file->getPathname(), \strlen($directory) + 1, -4);
            $class = 'TypeLang\\Type\\' . \str_replace('/', '\\', \str_replace('\\', '/', $relative));

            if (!\class_exists($class) || !\is_subclass_of($class, Node::class)) {
                continue;
            }

            if ((new \ReflectionClass($class))->isAbstract()) {
                continue;
            }

            $result[] = $class;
        }

        \sort($result);

        return $result;
    }

    /**
     * Guards the tree above from getting out of sync with the package: a
     * node that is not in the tree is a node whose cloning is not tested.
     */
    #[Test]
    public function treeHasANodeOfEveryClass(): void
    {
        $tree = self::tree();
        $actual = [];

        foreach (self::collect($tree) as $node) {
            $actual[$node::class] = true;

            // The namespace of a name is a value of the name
            if ($node instanceof Name && $node->namespace !== null) {
                $actual[$node->namespace::class] = true;
            }
        }

        $actual = \array_keys($actual);
        \sort($actual);

        self::assertSame(self::getNodeClasses(), $actual);
    }

    #[Test]
    public function cloneIsEqualToTheOriginal(): void
    {
        $tree = self::tree();

        self::assertEquals($tree, clone $tree);
    }

    #[Test]
    public function cloneSharesNoNodeWithTheOriginal(): void
    {
        $tree = self::tree();
        $clone = clone $tree;

        $original = self::collect($tree);
        $cloned = self::collect($clone);

        self::assertCount(\count($original), $cloned);
        self::assertSame([], \array_intersect_key($original, $cloned));
    }

    #[Test]
    public function changedCloneLeavesTheOriginalAsItIs(): void
    {
        $tree = self::tree();
        $expected = \serialize($tree);

        $clone = clone $tree;

        foreach (self::collect($clone) as $node) {
            foreach ((new \ReflectionObject($node))->getProperties() as $property) {
                if ($property->isStatic() || $property->isReadOnly()) {
                    continue;
                }

                $value = $property->getValue($node);

                match (true) {
                    \is_bool($value) => $property->setValue($node, !$value),
                    \is_array($value) => $property->setValue($node, \array_reverse($value)),
                    default => null,
                };
            }
        }

        self::assertNotEquals($tree, $clone);
        self::assertSame($expected, \serialize($tree));
    }

    #[Test]
    public function cloneOfANodeInsideATreeLeavesTheTreeAsItIs(): void
    {
        $tree = self::tree();
        $expected = self::collect($tree);

        $clone = clone $tree->types[1];

        self::assertSame([], \array_intersect_key($expected, self::collect($clone)));
        self::assertSame($expected, self::collect($tree));
    }

    /**
     * The value a node never changes is not a node of its own to clone.
     */
    #[Test]
    public function namespaceOfANameIsSharedWithTheClone(): void
    {
        $name = new Name('Any', new NamespaceName('Some'));
        $clone = clone $name;

        self::assertNotSame($name, $clone);
        self::assertSame($name->namespace, $clone->namespace);
    }
}
