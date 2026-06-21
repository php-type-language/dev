<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;

final class IdentifierTest extends TestCase
{
    #[Test]
    public function constructorStoresValue(): void
    {
        $id = new Identifier('MyClass');

        $this->assertSame('MyClass', $id->value);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $id = new Identifier('Foo');

        $this->assertSame(0, $id->offset);
    }

    #[Test]
    public function toStringReturnsValue(): void
    {
        $id = new Identifier('Foo');

        $this->assertSame('Foo', (string) $id);
        $this->assertSame('Foo', $id->toString());
    }

    #[Test]
    public function toLowerStringReturnsLowercasedValue(): void
    {
        $id = new Identifier('FooBar');

        $this->assertSame('foobar', $id->toLowerString());
    }

    #[Test]
    public function isVirtualIsTrueForHyphenatedName(): void
    {
        $id = new Identifier('non-empty-string');

        $this->assertTrue($id->isVirtual);
    }

    #[Test]
    public function isVirtualIsFalseForNormalName(): void
    {
        $id = new Identifier('string');

        $this->assertFalse($id->isVirtual);
    }

    #[Test]
    #[DataProvider('provideSpecialNames')]
    public function isSpecialIsTrueForSpecialClassNames(string $name): void
    {
        $id = new Identifier($name);

        $this->assertTrue($id->isSpecial);
    }

    public static function provideSpecialNames(): iterable
    {
        return [['self'], ['parent'], ['static'], ['SELF'], ['PARENT'], ['Static']];
    }

    #[Test]
    public function isSpecialIsFalseForRegularName(): void
    {
        $id = new Identifier('MyClass');

        $this->assertFalse($id->isSpecial);
    }

    #[Test]
    #[DataProvider('provideBuiltinNames')]
    public function isBuiltinIsTrueForBuiltinTypes(string $name): void
    {
        $id = new Identifier($name);

        $this->assertTrue($id->isBuiltin);
    }

    public static function provideBuiltinNames(): iterable
    {
        return [
            ['int'], ['string'], ['float'], ['bool'], ['null'],
            ['mixed'], ['object'], ['array'], ['void'], ['never'],
            ['callable'], ['iterable'], ['true'], ['false'],
            ['INT'], ['STRING'],
        ];
    }

    #[Test]
    public function isBuiltinIsFalseForCustomName(): void
    {
        $id = new Identifier('MyClass');

        $this->assertFalse($id->isBuiltin);
    }

    #[Test]
    public function createFromStringTrimsWhitespace(): void
    {
        $id = Identifier::createFromString('  Foo  ');

        $this->assertSame('Foo', $id->value);
    }

    #[Test]
    public function createFromStringReturnsSameInstanceWhenPassedIdentifier(): void
    {
        $id = new Identifier('Foo');
        $result = Identifier::createFromString($id);

        $this->assertSame($id, $result);
    }

    #[Test]
    public function createFromStringCreatesNewInstance(): void
    {
        $id = Identifier::createFromString('Bar');

        $this->assertInstanceOf(Identifier::class, $id);
        $this->assertSame('Bar', $id->value);
    }

    #[Test]
    public function createFromStringThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Identifier::createFromString('');
    }

    #[Test]
    public function createFromStringThrowsOnWhitespaceOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Identifier::createFromString('   ');
    }

    #[Test]
    public function isLooksLikeSpecialReturnsTrueForSpecialNames(): void
    {
        $this->assertTrue(Identifier::isLooksLikeSpecial('self'));
        $this->assertTrue(Identifier::isLooksLikeSpecial('SELF'));
        $this->assertTrue(Identifier::isLooksLikeSpecial('parent'));
        $this->assertTrue(Identifier::isLooksLikeSpecial('static'));
    }

    #[Test]
    public function isLooksLikeSpecialReturnsFalseForOtherNames(): void
    {
        $this->assertFalse(Identifier::isLooksLikeSpecial('Foo'));
        $this->assertFalse(Identifier::isLooksLikeSpecial('int'));
    }

    #[Test]
    public function isLooksLikeBuiltinReturnsTrueForBuiltinNames(): void
    {
        $this->assertTrue(Identifier::isLooksLikeBuiltin('int'));
        $this->assertTrue(Identifier::isLooksLikeBuiltin('INT'));
        $this->assertTrue(Identifier::isLooksLikeBuiltin('string'));
    }

    #[Test]
    public function isLooksLikeBuiltinReturnsFalseForCustomNames(): void
    {
        $this->assertFalse(Identifier::isLooksLikeBuiltin('Foo'));
        $this->assertFalse(Identifier::isLooksLikeBuiltin('self'));
    }

    #[Test]
    public function serializeAndUnserializeRoundtrip(): void
    {
        $id = new Identifier('MyClass');
        $id->offset = 42;

        /** @var Identifier $restored */
        $restored = \unserialize(\serialize($id));

        $this->assertInstanceOf(Identifier::class, $restored);
        $this->assertSame('MyClass', $restored->value);
        $this->assertSame(42, $restored->offset);
    }
}
