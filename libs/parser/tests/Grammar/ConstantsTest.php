<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Grammar;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Constants of classes, global constants and their masks (grammar/constants.pp3).
 */
final class ConstantsTest extends GrammarTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::markTestIncomplete('TODO');
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validConstantDataProvider(): iterable
    {
        yield 'constant' => ['Foo::BAR'];
        yield 'constant in lower case' => ['Foo::bar'];
        yield 'constant with a namespace' => ['Foo\\Bar::BAZ'];
        yield 'constant with a fully qualified class' => ['\\Foo\\Bar::BAZ'];
        yield 'constant of self' => ['self::BAR'];
        yield 'constant of static' => ['static::BAR'];
        yield 'constant of parent' => ['parent::BAR'];
        yield 'class name' => ['Foo::class'];
        yield 'class name of static' => ['static::class'];
        yield 'literal word as a constant' => ['Foo::true'];
        yield 'keyword as a constant' => ['Foo::is'];
        yield 'builtin as a class' => ['int::BAR'];
        yield 'spaces around the colons' => ['Foo :: BAR'];

        yield 'mask of a class' => ['Foo::*'];
        yield 'mask with a suffix' => ['Foo::BAR_*'];
        yield 'mask with a prefix' => ['Foo::*_BAZ'];
        yield 'mask inside' => ['Foo::B*R'];
        yield 'mask with two stars' => ['Foo::A*B*'];
        yield 'mask on both ends' => ['Foo::*BAR*'];
        yield 'mask of a fully qualified class' => ['\\Foo::BAR_*'];
        yield 'mask of self' => ['self::*'];

        yield 'global mask' => ['PHP_*'];
        yield 'global mask with a prefix' => ['*_SUFFIX'];
        yield 'global mask inside' => ['PHP_*_MAX'];
        yield 'fully qualified global mask' => ['\\PHP_*'];
        yield 'namespaced global mask' => ['\\Foo\\BAR_*'];
        yield 'namespaced mask without a prefix' => ['Foo\\Bar\\BAZ_*'];
        yield 'mask of a whole namespace' => ['Path\\To\\*'];
        yield 'mask with a suffix in a namespace' => ['Path\\To\\*_SUFFIX'];
        yield 'mask of a whole fully qualified namespace' => ['\\Path\\To\\*'];

        yield 'global constant' => ['PHP_EOL'];
        yield 'namespaced global constant' => ['Foo\\BAR'];
    }

    #[DataProvider('validConstantDataProvider')]
    public function testConstantIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidConstantDataProvider(): iterable
    {
        yield 'no constant' => ['Foo::'];
        yield 'one colon' => ['Foo:BAR'];
        yield 'three colons' => ['Foo:::BAR'];
        yield 'constant of a constant' => ['Foo::BAR::BAZ'];
        yield 'constant of this' => ['$this::BAR'];
        yield 'constant starts with a digit' => ['Foo::1'];
        yield 'two stars in a row' => ['Foo::**'];
        yield 'two stars in a row inside' => ['Foo::A**B'];
        yield 'two stars in a row behind a name' => ['A**'];
        yield 'lone star' => ['*'];
        yield 'two stars' => ['**'];
        yield 'two stars behind a name' => ['Foo::BAR**'];
        yield 'constant of a mask' => ['Foo::*::BAR'];
        yield 'literal as a class' => ['true::BAR'];
    }

    #[DataProvider('invalidConstantDataProvider')]
    public function testConstantIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
