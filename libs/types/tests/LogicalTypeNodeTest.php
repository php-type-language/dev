<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;

final class LogicalTypeNodeTest extends TestCase
{
    private function type(string $name): NamedTypeNode
    {
        return new NamedTypeNode(TypeName::from($name));
    }

    /**
     * @return iterable<non-empty-string, array{class-string<LogicalTypeNode>}>
     */
    public static function provideLogicalTypes(): iterable
    {
        yield UnionTypeNode::class => [UnionTypeNode::class];
        yield IntersectionTypeNode::class => [IntersectionTypeNode::class];
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function twoStatementsAreEnough(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);

        self::assertCount(2, $node);
        self::assertCount(2, $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function anArbitraryNumberOfStatementsIsStored(string $class): void
    {
        $node = new $class([
            $this->type('A'),
            $this->type('B'),
            $this->type('C'),
            $this->type('D'),
        ]);

        self::assertCount(4, $node);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function typesOrderIsPreserved(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class([$a, $b, $c]);

        self::assertSame([$a, $b, $c], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function typesAreAcceptedFromAnyTraversable(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class(new \ArrayIterator([$a, $b]));

        self::assertSame([$a, $b], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function typesAreAcceptedFromAGenerator(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class((static function () use ($a, $b): \Generator {
            yield $a;
            yield $b;
        })());

        self::assertSame([$a, $b], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function typesAreReindexedIntoAList(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class([7 => $a, 42 => $b]);

        self::assertSame([$a, $b], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function aSingleStatementIsRejected(string $class): void
    {
        $this->expectException(\LogicException::class);

        new $class([$this->type('A')]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function noStatementsAreRejected(string $class): void
    {
        $this->expectException(\LogicException::class);

        new $class([]);
    }

    /**
     * A logical type built out of a single type of the same kind is the very
     * same type: the nested one is flattened into it.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function aSingleNestedStatementIsUnwrapped(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class([new $class([$a, $b])]);

        self::assertSame([$a, $b], $node->types);
    }

    /**
     * A nested type is flattened before the number of types is
     * checked, so a single type wrapped into a logical type is still too little.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function aStatementFlattenedIntoASingleTypeIsRejected(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);
        $node->types = [$this->type('C')];

        $this->expectException(\LogicException::class);

        new $class([$node]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function nestedStatementsOfTheSameTypeAreFlattened(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class([new $class([$a, $b]), $c]);

        self::assertSame([$a, $b, $c], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function deeplyNestedStatementsAreFlattened(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');
        $d = $this->type('D');

        $node = new $class([new $class([new $class([$a, $b]), $c]), $d]);

        self::assertSame([$a, $b, $c, $d], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function nestedStatementsAreFlattenedInAnyPosition(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class([$a, new $class([$b, $c])]);

        self::assertSame([$a, $b, $c], $node->types);
    }

    #[Test]
    public function unionDoesNotFlattenIntersection(): void
    {
        $intersection = new IntersectionTypeNode([$this->type('A'), $this->type('B')]);

        $node = new UnionTypeNode([$intersection, $this->type('C')]);

        self::assertCount(2, $node);
        self::assertSame($intersection, $node->types[0]);
    }

    #[Test]
    public function intersectionDoesNotFlattenUnion(): void
    {
        $union = new UnionTypeNode([$this->type('A'), $this->type('B')]);

        $node = new IntersectionTypeNode([$union, $this->type('C')]);

        self::assertCount(2, $node);
        self::assertSame($union, $node->types[0]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function iteratorYieldsStatements(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($node->getIterator()));
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function serializationRoundtripPreservesStatementsAndOffset(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')], 13);

        /** @var LogicalTypeNode $restored */
        $restored = \unserialize(\serialize($node));

        self::assertInstanceOf($class, $restored);
        self::assertSame(13, $restored->offset);
        self::assertCount(2, $restored);
        self::assertSame('A', $restored->types[0]->name->toString());
        self::assertSame('B', $restored->types[1]->name->toString());
    }

    /**
     * Building a logical type out of an already built one must not cost more the
     * longer it gets: the flattening is linear in the number of types.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function flatteningIsLinear(string $class): void
    {
        $types = [];

        for ($i = 0; $i < 1000; ++$i) {
            $types[] = $this->type('T' . $i);
        }

        $node = new $class($types);

        self::assertCount(1000, $node);
        self::assertSame($types, $node->types);
    }

    /**
     * @return iterable<non-empty-string, array{mixed}>
     */
    public static function provideInvalidOffsets(): iterable
    {
        yield 'negative int' => [-1];
        yield 'numeric string' => ['0'];
        yield 'float' => [1.5];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessReadsTypes(string $class): void
    {
        $a = $this->type('A');
        $node = new $class([$a, $this->type('B')]);

        self::assertTrue(isset($node[0]));
        self::assertFalse(isset($node[2]));
        self::assertSame($a, $node[0]);
        self::assertNull($node[2]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessReplacesAndAppendsTypes(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');
        $d = $this->type('D');
        $node = new $class([$a, $b]);

        $node[0] = $c;
        $node[] = $d;

        self::assertSame([$c, $b, $d], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessSetBeyondTheEndKeepsTheListSequential(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');
        $node = new $class([$a, $b]);

        $node[7] = $c;

        self::assertSame([$a, $b, $c], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessUnsetReindexesTheTypes(string $class): void
    {
        $a = $this->type('A');
        $c = $this->type('C');
        $node = new $class([$a, $this->type('B'), $c]);

        unset($node[1]);

        self::assertSame([$a, $c], $node->types);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessUnsetBelowTwoTypesIsRejected(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);

        $this->expectException(\InvalidArgumentException::class);

        unset($node[0]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessRejectsNonNodeValue(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);

        $this->expectException(\InvalidArgumentException::class);

        $node->offsetSet(null, 'not a node');
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function arrayAccessRejectsInvalidOffsets(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);

        foreach (self::provideInvalidOffsets() as [$offset]) {
            foreach (['offsetExists', 'offsetGet', 'offsetUnset'] as $method) {
                try {
                    $node->$method($offset);
                } catch (\InvalidArgumentException) {
                    continue;
                }

                self::fail(\sprintf('%s() accepted %s', $method, \var_export($offset, true)));
            }
        }

        $this->expectException(\InvalidArgumentException::class);

        $node->offsetSet(-1, $this->type('C'));
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function iteratorYieldsTheTypesInOrder(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $node = new $class([$a, $b]);

        self::assertSame([$a, $b], \iterator_to_array($node));
    }
}
