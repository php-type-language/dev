<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Grammar;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Callable types: parameters, defaults and return types (grammar/callable.pp3).
 */
final class CallableTest extends GrammarTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::markTestIncomplete('TODO');
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validCallableDataProvider(): iterable
    {
        yield 'no parameters' => ['callable(): void'];
        yield 'no return type' => ['callable(int)'];
        yield 'no parameters and no return type' => ['callable()'];
        yield 'one parameter' => ['callable(int): void'];
        yield 'space before the colon' => ['callable(int) : void'];
        yield 'trailing comma' => ['callable(int,): void'];
        yield 'two parameters' => ['callable(int, string): void'];
        yield 'closure' => ['Closure(int): void'];
        yield 'fully qualified closure' => ['\\Closure(int): void'];
        yield 'pure callable' => ['pure-callable(int): int'];
        yield 'pure closure' => ['pure-Closure(int): int'];
        yield 'any name' => ['foo(T): void'];

        yield 'named parameter' => ['callable(int $a): void'];
        yield 'name only' => ['callable($a): void'];
        yield 'by reference' => ['callable(int &$a): void'];
        yield 'by reference without a name' => ['callable(int&): void'];
        yield 'by reference without a type' => ['callable(&$a): void'];
        yield 'by reference of a union' => ['callable(A &$x): void'];
        yield 'intersection as a type' => ['callable(A&B $x): void'];

        yield 'variadic' => ['callable(int...): void'];
        yield 'variadic with a space' => ['callable(int ...): void'];
        yield 'variadic with a name' => ['callable(int ...$a): void'];
        yield 'variadic with a name glued to a type' => ['callable(int... $a): void'];
        yield 'variadic by reference' => ['callable(int &...$a): void'];
        yield 'variadic is not the last' => ['callable(int..., string): void'];

        yield 'optional' => ['callable(int=): void'];
        yield 'optional with a name' => ['callable(int $a=): void'];
        yield 'optional by reference' => ['callable(int &$a=): void'];
        yield 'optional before a required' => ['callable(int=, string): void'];
        yield 'default integer' => ['callable(int $a = 42): void'];
        yield 'default negative integer' => ['callable(int $a = -1): void'];
        yield 'default float' => ['callable(float $a = 1.5): void'];
        yield 'default string' => ["callable(string \$a = 'x'): void"];
        yield 'default true' => ['callable(bool $a = true): void'];
        yield 'default null' => ['callable(?int $a = null): void'];
        yield 'default class constant' => ['callable(int $a = Foo::BAR): void'];
        yield 'default class name' => ['callable(string $a = Foo::class): void'];
        yield 'default global constant' => ['callable(int $a = PHP_INT_MAX): void'];
        yield 'default fully qualified constant' => ['callable(string $a = \\PHP_EOL): void'];
        yield 'default without a name' => ['callable(int = 42): void'];
        yield 'defaults of two' => ['callable(int $a = 42, string $b = "x",): void'];
        yield 'default of a variadic, the node refuses it' => ['callable(int ...$a = 1): void'];

        yield 'return union' => ['callable(): int|string'];
        yield 'return nullable' => ['callable(): ?int'];
        yield 'return list' => ['callable(): int[]'];
        yield 'return offset' => ['callable(): T[K]'];
        yield 'return generic' => ['callable(): array<int, string>'];
        yield 'return shape' => ['callable(): array{a: int}'];
        yield 'return this' => ['callable(): $this'];
        yield 'return static' => ['callable(): static'];
        yield 'return grouped' => ['callable(): (int|string)'];
        yield 'return callable' => ['callable(): callable(): void'];
        yield 'return condition spreads on the callable' => ['callable(): T is int ? A : B'];
        yield 'return grouped condition' => ['callable(): (T is int ? A : B)'];

        yield 'shape parameters' => ['callable(array{a: int} $a, list<int> ...$b): array{b: string}'];
        yield 'callable as a parameter' => ['callable(callable(int): void $cb): void'];
        yield 'condition as a parameter' => ['callable(T is int ? A : B $x): void'];
        yield 'condition on a parameter' => ['callable(int $a): ($a is int ? A : B)'];
        yield 'nullable callable' => ['?callable(): int'];
        yield 'list of callables' => ['(callable(): int)[]'];
        yield 'callable in a union' => ['callable(): int|string'];
    }

    #[DataProvider('validCallableDataProvider')]
    public function testCallableIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidCallableDataProvider(): iterable
    {
        yield 'space before the parenthesis' => ['callable (int): void'];
        yield 'line break before the parenthesis' => ["callable\n(int): void"];
        yield 'comment before the parenthesis' => ['callable/* c */(int): void'];
        yield 'space before the parenthesis of a closure' => ['Closure (int): void'];
        yield 'space before the parenthesis of an empty list' => ['callable (): void'];
        yield 'unclosed' => ['callable('];
        yield 'unclosed after a parameter' => ['callable(int'];
        yield 'no return type behind a colon' => ['callable(int):'];
        yield 'only a colon' => ['callable(): '];
        yield 'extra parenthesis' => ['callable(int)) void'];
        yield 'only a comma' => ['callable(,)'];
        yield 'empty parameter' => ['callable(int,,)'];

        yield 'variadic behind a name' => ['callable(int $a...): void'];
        yield 'by reference before a type' => ['callable(&int): void'];
        yield 'by reference behind a name' => ['callable(int $a&): void'];
        yield 'variadic behind by reference' => ['callable(int ...&): void'];
        yield 'no name behind a dollar' => ['callable(int $): void'];
        yield 'optional before a type' => ['callable(=int): void'];
        yield 'optional alone' => ['callable(= 42): void'];
        yield 'optional behind a variadic marker' => ['callable(int=...): void'];

        yield 'default is an expression' => ['callable(int $a = 1 + 2): void'];
        yield 'default is an array' => ['callable(array $a = []): void'];
        yield 'default is a variable' => ['callable(int $a = $b): void'];
        yield 'default is a call' => ['callable(int $a = foo()): void'];
        yield 'default is a union' => ['callable(int $a = Foo::A|Foo::B): void'];
        yield 'double assignment' => ['callable(int $a == 42): void'];
    }

    #[DataProvider('invalidCallableDataProvider')]
    public function testCallableIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
