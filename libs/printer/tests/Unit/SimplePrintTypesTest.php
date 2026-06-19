<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\Parser;
use TypeLang\Printer\NativeTypePrinter;
use TypeLang\Printer\PrettyPrinter;

#[Group('unit'), Group('type-lang/printer')]
final class SimplePrintTypesTest extends TestCase
{
    private static function typesList(): iterable
    {
        yield 'example-type';
        yield 'Non\Qualified\Name';
        yield '\Full\Qualified\Name';

        yield 'Generic<T>' => 'Generic';
        yield 'Generic<hint T>' => 'Generic';

        yield '$this' => 'self';
        yield '$this|null|string' => 'self|null|string';

        yield 'Example|Union|Type';
        yield 'Example & Intersection & Type' => 'Example&Intersection&Type';

        yield '?NullableType' => '?NullableType';
        yield 'ListType[]' => 'iterable';

        yield 'GLOBAL_CONST';
        yield 'Namespaced\GLOBAL_CONST';
        yield '\Full\Qualified\CONST';

        yield 'GLOBAL_CONST_MASK_*' => 'mixed';
        yield 'Namespaced\GLOBAL_CONST_MASK_*' => 'mixed';
        yield '\Full\Qualified\CONST_MASK_*' => 'mixed';

        yield 'ClassName::CONST' => 'mixed';
        yield 'Namespaced\ClassName::CONST' => 'mixed';
        yield '\Full\Qualified\ClassName::CONST' => 'mixed';

        yield 'ClassName::CONST_MASK_*' => 'mixed';
        yield 'Namespaced\ClassName::CONST_MASK_*' => 'mixed';
        yield '\Full\Qualified\ClassName::CONST_MASK_*' => 'mixed';

        yield 'callable()' => 'callable';
        yield 'callable(): With\Type' => 'callable';
        yield 'callable(Param): With\Type' => 'callable';
        yield 'callable(ParamNamed $named): With\Type' => 'callable';
        yield 'callable(Optional=): With\Type' => 'callable';
        yield 'callable(OptionalNamed $name=): With\Type' => 'callable';
        yield 'callable(Out&): With\Type' => 'callable';
        yield 'callable(OutNamed &$name): With\Type' => 'callable';
        yield 'callable(OutOptional&=): With\Type' => 'callable';
        yield 'callable(OutOptionalNamed &$name=): With\Type' => 'callable';
        yield 'callable(Variadic...): With\Type' => 'callable';
        yield 'callable(VariadicNamed ...$name): With\Type' => 'callable';
        yield 'callable(OutVariadic &...$name): With\Type' => 'callable';

        yield '(T is U ? V : W)' => 'V|W';
        yield '(T is not U ? V : W)' => 'V|W';
        yield '(T > U ? V : W)' => 'V|W';
        yield '(T < U ? V : W)' => 'V|W';
        yield '(T >= U ? V : W)' => 'V|W';
        yield '(T <= U ? V : W)' => 'V|W';

        yield '($var is foo() ? T : class-string<T>)' => 'T|string';

        yield 'Shape{}' => 'Shape';
        yield <<<'PHP'
            Shape{
                k: T,
                v: U
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                k: T,
                v: U,
                ...
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                k: T,
                v: U,
                ...<T1, T2>
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                k?: T,
                v?: U
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                k?: T,
                v?: U,
                ...
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                k?: T,
                v?: U,
                ...<T1, T2>
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                T,
                U
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                T,
                U,
                ...
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                T,
                U,
                ...<T1>
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                0: T,
                1: U
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                0: T,
                1: U,
                ...
            }
            PHP => 'Shape';
        yield <<<'PHP'
            Shape{
                0: T,
                1: U,
                ...<T1>
            }
            PHP => 'Shape';
    }

    public static function prettyPrintableTypesDataProvider(): iterable
    {
        $parser = new Parser();

        foreach (self::typesList() as $type => $native) {
            if (\is_int($type)) {
                $type = $native;
            }

            yield $type => [$parser->parse($type), $type];
        }
    }

    #[DataProvider('prettyPrintableTypesDataProvider')]
    public function testPrettyPrinting(TypeStatement $stmt, string $expected): void
    {
        $printer = new PrettyPrinter(
            wrapUnionType: false,
        );

        self::assertSame($expected, $printer->print($stmt));
    }

    public static function nativePrintableTypesDataProvider(): iterable
    {
        $parser = new Parser();

        foreach (self::typesList() as $type => $native) {
            if (\is_int($type)) {
                $type = $native;
            }

            yield $type => [$parser->parse($type), $native];
        }
    }

    #[DataProvider('nativePrintableTypesDataProvider')]
    public function testNativePrettyPrinting(TypeStatement $stmt, string $expected): void
    {
        $printer = new NativeTypePrinter();

        self::assertSame($expected, $printer->print($stmt));
    }
}
