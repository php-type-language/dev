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
    public function constructorRequiresAtLeastTwoStatements(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));

        self::assertCount(2, $node);
        self::assertCount(2, $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function variadicStatementsAreStored(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'), $this->type('C'), $this->type('D'));

        self::assertCount(4, $node);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function statementsOrderIsPreserved(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class($a, $b, $c);

        self::assertSame([$a, $b, $c], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function nestedStatementsOfTheSameTypeAreFlattened(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class(new $class($a, $b), $c);

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

        $node = new $class(new $class(new $class($a, $b), $c), $d);

        self::assertSame([$a, $b, $c, $d], $node->statements);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function nestedStatementsAreFlattenedInAnyPosition(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');
        $c = $this->type('C');

        $node = new $class($a, new $class($b, $c));

        self::assertSame([$a, $b, $c], $node->statements);
    }

    #[Test]
    public function unionDoesNotFlattenIntersection(): void
    {
        $intersection = new IntersectionTypeNode($this->type('A'), $this->type('B'));

        $node = new UnionTypeNode($intersection, $this->type('C'));

        self::assertCount(2, $node);
        self::assertSame($intersection, $node->statements[0]);
    }

    #[Test]
    public function intersectionDoesNotFlattenUnion(): void
    {
        $union = new UnionTypeNode($this->type('A'), $this->type('B'));

        $node = new IntersectionTypeNode($union, $this->type('C'));

        self::assertCount(2, $node);
        self::assertSame($union, $node->statements[0]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function iteratorYieldsStatements(string $class): void
    {
        $a = $this->type('A');
        $b = $this->type('B');

        $node = new $class($a, $b);

        self::assertSame([$a, $b], \iterator_to_array($node->getIterator()));
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function serializationRoundtripPreservesStatementsAndOffset(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));
        $node->offset = 13;

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
    public function serializePayloadContainsOffsetAndStatements(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));
        $node->offset = 7;

        self::assertSame([7, $node->statements], $node->__serialize());
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function unserializeThrowsWhenOffsetIsMissing(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));

        $this->expectException(\UnexpectedValueException::class);

        $node->__unserialize([]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function unserializeThrowsWhenStatementsAreMissing(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));

        $this->expectException(\UnexpectedValueException::class);

        $node->__unserialize([0]);
    }

    #[Test]
    #[DataProvider('provideLogicalTypes')]
    public function defaultOffsetIsZero(string $class): void
    {
        $node = new $class($this->type('A'), $this->type('B'));

        self::assertSame(0, $node->offset);
    }
}
