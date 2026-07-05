<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\UnionTypeNode;

final class UnionTypeNodeTest extends TestCase
{
    private function named(string $name): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    #[Test]
    public function constructorWithTwoTypes(): void
    {
        $a = $this->named('int');
        $b = $this->named('string');
        $node = new UnionTypeNode($a, $b);

        self::assertCount(2, $node);
        self::assertSame([$a, $b], $node->statements);
    }

    #[Test]
    public function constructorWithThreeTypes(): void
    {
        $a = $this->named('int');
        $b = $this->named('string');
        $c = $this->named('null');
        $node = new UnionTypeNode($a, $b, $c);

        self::assertCount(3, $node);
    }

    #[Test]
    public function nestedUnionIsFlattened(): void
    {
        $a = $this->named('int');
        $b = $this->named('string');
        $c = $this->named('null');

        $inner = new UnionTypeNode($a, $b);
        $outer = new UnionTypeNode($inner, $c);

        self::assertCount(3, $outer);
        self::assertSame([$a, $b, $c], $outer->statements);
    }

    #[Test]
    public function iteratorYieldsStatements(): void
    {
        $a = $this->named('int');
        $b = $this->named('string');
        $node = new UnionTypeNode($a, $b);

        self::assertSame([$a, $b], \iterator_to_array($node));
    }

    #[Test]
    public function countReturnsNumberOfStatements(): void
    {
        $node = new UnionTypeNode($this->named('int'), $this->named('string'), $this->named('bool'));

        self::assertSame(3, $node->count());
    }

    #[Test]
    public function intersectionTypeNodeIsNotFlattenedInsideUnion(): void
    {
        $a = $this->named('A');
        $b = $this->named('B');
        $c = $this->named('C');

        $inner = new IntersectionTypeNode($a, $b);
        $outer = new UnionTypeNode($inner, $c);

        self::assertCount(2, $outer);
        self::assertSame($inner, $outer->statements[0]);
    }

    #[Test]
    public function serializeAndUnserializeRoundtrip(): void
    {
        $a = $this->named('int');
        $b = $this->named('string');
        $node = new UnionTypeNode($a, $b);
        $node->offset = 5;

        /** @var UnionTypeNode $restored */
        $restored = \unserialize(\serialize($node));

        self::assertInstanceOf(UnionTypeNode::class, $restored);
        self::assertCount(2, $restored);
        self::assertSame(5, $restored->offset);
    }
}
