<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;

final class IntersectionTypeNodeTest extends TestCase
{
    private function named(string $name): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    #[Test]
    public function constructorWithTwoTypes(): void
    {
        $a = $this->named('Countable');
        $b = $this->named('Stringable');
        $node = new IntersectionTypeNode($a, $b);

        $this->assertCount(2, $node);
        $this->assertSame([$a, $b], $node->statements);
    }

    #[Test]
    public function constructorWithThreeTypes(): void
    {
        $a = $this->named('A');
        $b = $this->named('B');
        $c = $this->named('C');
        $node = new IntersectionTypeNode($a, $b, $c);

        $this->assertCount(3, $node);
    }

    #[Test]
    public function nestedIntersectionIsFlattened(): void
    {
        $a = $this->named('A');
        $b = $this->named('B');
        $c = $this->named('C');

        $inner = new IntersectionTypeNode($a, $b);
        $outer = new IntersectionTypeNode($inner, $c);

        $this->assertCount(3, $outer);
        $this->assertSame([$a, $b, $c], $outer->statements);
    }

    #[Test]
    public function iteratorYieldsStatements(): void
    {
        $a = $this->named('Countable');
        $b = $this->named('Stringable');
        $node = new IntersectionTypeNode($a, $b);

        $this->assertSame([$a, $b], \iterator_to_array($node));
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new IntersectionTypeNode($this->named('A'), $this->named('B'));

        $this->assertSame(0, $node->offset);
    }
}
