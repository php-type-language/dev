<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\UnionTypeNode;

final class LogicalTypeNodeTest extends TestCase
{
    private function type(string $name): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
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
        self::assertCount(2, $node->statements);
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
    public function statementsOrderIsPreserved(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class([$a, $b, $c]);

        self::assertSame([$a, $b, $c], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function statementsAreAcceptedFromAnyTraversable(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class(new \ArrayIterator([$a, $b]));

        self::assertSame([$a, $b], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function statementsAreAcceptedFromAGenerator(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class((static function () use ($a, $b): \Generator {
            yield $a;
            yield $b;
        })());

        self::assertSame([$a, $b], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function statementsAreReindexedIntoAList(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class([7 => $a, 42 => $b]);

        self::assertSame([$a, $b], $node->statements);
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
     * A statement built out of a single statement of the same kind is the very
     * same statement: the nested one is flattened into it.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function aSingleNestedStatementIsUnwrapped(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class([new $class([$a, $b])]);

        self::assertSame([$a, $b], $node->statements);
    }

    /**
     * A nested statement is flattened before the number of statements is
     * checked, so a single type wrapped into a statement is still too little.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function aStatementFlattenedIntoASingleTypeIsRejected(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);
        $node->statements = [$this->type('C')];

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

        self::assertSame([$a, $b, $c], $node->statements);
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

        self::assertSame([$a, $b, $c, $d], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function nestedStatementsAreFlattenedInAnyPosition(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class([$a, new $class([$b, $c])]);

        self::assertSame([$a, $b, $c], $node->statements);
    }

    #[Test]
    public function unionDoesNotFlattenIntersection(): void
    {
        $intersection = new IntersectionTypeNode([$this->type('A'), $this->type('B')]);

        $node = new UnionTypeNode([$intersection, $this->type('C')]);

        self::assertCount(2, $node);
        self::assertSame($intersection, $node->statements[0]);
    }

    #[Test]
    public function intersectionDoesNotFlattenUnion(): void
    {
        $union = new UnionTypeNode([$this->type('A'), $this->type('B')]);

        $node = new IntersectionTypeNode([$union, $this->type('C')]);

        self::assertCount(2, $node);
        self::assertSame($union, $node->statements[0]);
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
        self::assertSame('A', $restored->statements[0]->name->toString());
        self::assertSame('B', $restored->statements[1]->name->toString());
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function theOffsetIsPassedThroughTheConstructor(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')], 42);

        self::assertSame(42, $node->offset);
        self::assertSame(42, $node->getOffset());
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function defaultOffsetIsZero(string $class): void
    {
        $node = new $class([$this->type('A'), $this->type('B')]);

        self::assertSame(0, $node->offset);
    }

    /**
     * Building a statement out of an already built one must not cost more the
     * longer it gets: the flattening is linear in the number of statements.
     */
    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function flatteningIsLinear(string $class): void
    {
        $statements = [];

        for ($i = 0; $i < 1000; ++$i) {
            $statements[] = $this->type('T' . $i);
        }

        $node = new $class($statements);

        self::assertCount(1000, $node);
        self::assertSame($statements, $node->statements);
    }
}
