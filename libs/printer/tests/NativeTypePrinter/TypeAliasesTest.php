<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NativeTypePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NativeTypePrinter\TypeAliases;
use TypeLang\Printer\Tests\TestCase;
use TypeLang\TypeSystem\Constraint\RangeConstraint;
use TypeLang\TypeSystem\Dialect;
use TypeLang\TypeSystem\Native;
use TypeLang\TypeSystem\Registry;
use TypeLang\TypeSystem\Type\AtomicType;

#[CoversClass(TypeAliases::class)]
final class TypeAliasesTest extends TestCase
{
    public function testBuiltinAliasIsResolved(): void
    {
        self::assertSame('int', (new TypeAliases())->resolve('positive-int'));
    }

    public function testNameWithNoAliasIsReturnedAsIs(): void
    {
        self::assertSame('Some\Any', (new TypeAliases())->resolve('Some\Any'));
    }

    public function testNameIsResolvedInAnyCase(): void
    {
        self::assertSame('int', (new TypeAliases())->resolve('POSITIVE-INT'));
    }

    public function testAddedAliasOverridesTheBuiltinOne(): void
    {
        $aliases = new TypeAliases();
        $aliases->add('positive-int', 'float');

        self::assertSame('float', $aliases->resolve('positive-int'));
    }

    public function testUnionAliasIsWrittenWithSortedTypes(): void
    {
        $aliases = new TypeAliases();
        $aliases->addUnion('Number', ['int', 'float']);

        self::assertSame('float|int', $aliases->resolve('Number'));
    }

    public function testIntersectionAliasIsWrittenWithSortedTypes(): void
    {
        $aliases = new TypeAliases();
        $aliases->addIntersection('Both', ['B', 'A']);

        self::assertSame('A&B', $aliases->resolve('Both'));
    }
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function builtinDataProvider(): iterable
    {
        yield 'native' => ['int', 'int'];
        yield 'native in any case' => ['STRING', 'string'];
        yield 'qualified string' => ['non-empty-lowercase-string', 'string'];
        yield 'interval' => ['non-zero-int', 'int'];
        yield 'list' => ['non-empty-list', 'array'];
        yield 'class-string' => ['class-string', 'string'];
        yield 'union' => ['scalar', 'bool|float|int|string'];
        yield 'numeric' => ['numeric', 'int|float|string'];
        yield 'never' => ['no-return', 'never'];
        yield 'resource' => ['resource', 'mixed'];
        yield 'class' => ['\Closure', '\Closure'];
        yield 'class by pure-closure' => ['pure-closure', '\Closure'];
        yield 'union with a class' => ['__stringandstringable', 'string|\Stringable'];
        yield 'bool is kept for the union printer' => ['true', 'true'];
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $expected
     */
    #[DataProvider('builtinDataProvider')]
    public function testBuiltinNamesAreResolvedByTheTypeSystem(string $name, string $expected): void
    {
        self::assertSame($expected, (new TypeAliases())->resolve($name));
    }

    public function testRegistryOfTheTypeSystemIsUsed(): void
    {
        $registry = new Registry([Dialect::Php]);
        $registry->define('Money', new AtomicType(Native::Int, [new RangeConstraint(0)]));

        $aliases = new TypeAliases($registry);

        self::assertSame('int', $aliases->resolve('money'));
        self::assertSame('non-empty-string', $aliases->resolve('non-empty-string'));
    }
}
