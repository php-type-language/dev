<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Traverser;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Type\Node;

/**
 * A type that is printed and read again is the same tree.
 */
final class RoundTripTest extends TestCase
{
    private static function dump(Node $node): string
    {
        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$node]);

        return \trim($visitor->output);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function stableDataProvider(): iterable
    {
        yield 'name' => ['\Foo\Bar'];
        yield 'virtual name' => ['non-empty-string'];
        yield 'int' => ['42'];
        yield 'float' => ['-1.5e3'];
        yield 'string' => ["'a'"];
        yield 'null' => ['null'];
        yield 'this' => ['$this'];
        yield 'class constant' => ['Foo::BAR'];
        yield 'class constant mask' => ['Foo::BAR_*'];
        yield 'global constant mask' => ['PHP_*'];
        yield 'nullable' => ['?int'];
        yield 'list' => ['int[]'];
        yield 'list of a group' => ['(A|B)[]'];
        yield 'offset access' => ['T[K]'];
        yield 'union' => ['int|string'];
        yield 'intersection' => ['A&B'];
        yield 'union of intersections' => ['A&B|C&D'];
        yield 'generic' => ['Foo<int, string>'];
        yield 'generic with a variance and a wildcard' => ['Foo<covariant A, *>'];
        yield 'shape' => ['array{a: int, b?: string}'];
        yield 'shape with keys' => ["array{0: A, 'b': B, Foo::BAR: C}"];
        yield 'unsealed shape' => ['array{a: int, ...<K, V>}'];
        yield 'callable' => ['callable(int $a, string ...$b): void'];
        yield 'callable with by reference and default' => ['callable(int &$a, string $b = 42): ?A[]'];
        yield 'callable with templates' => ['callable<T of int>(T): T'];
        yield 'callable with template bounds' => ['callable<covariant T of A super B = C>(T): T'];
        yield 'condition' => ['T is int ? A : B'];
        yield 'negated condition' => ['T is not int ? A : B'];
        yield 'condition with variables' => ['$a is $b ? C : D'];
        yield 'negated nullable target' => ['T is not ?A ? X : Y'];
        yield 'nested condition' => ['T is int ? A : B is C ? D : E'];
        yield 'list of a group of a nullable' => ['(?int)[]'];
        yield 'list of a group of a callable' => ['(callable(): int)[]'];
        yield 'offset access of a group of a nullable' => ['(?T)[K]'];
        yield 'nullable of a group of a nullable' => ['?(?int)'];
        yield 'nullable of a nullable list' => ['?(?int[])'];
        yield 'list of a callable with no return type' => ['(callable(int))[]'];
        yield 'negated target of a group of a nullable' => ['T is not (?A)[] ? X : Y'];
        yield 'parameter with a name only' => ['callable($a): void'];
        yield 'parameters with a name only and the marks' => ['callable(&$a, ...$b, &...$c, $d = 42): void'];
        yield 'variadic type with no name' => ['callable(...int, $a)'];
    }

    /**
     * @param non-empty-string $source
     * @throws \Throwable
     */
    #[DataProvider('stableDataProvider')]
    public function testPrintedTypeIsReadAsTheSameTree(string $source): void
    {
        $printed = (new PrettyTypePrinter())->print(self::parse($source));

        self::assertSame(self::dump(self::parse($source)), self::dump(self::parse($printed)), $printed);
    }
}
