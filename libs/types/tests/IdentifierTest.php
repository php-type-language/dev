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

        self::assertSame('MyClass', $id->value);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $id = new Identifier('Foo');

        self::assertSame(0, $id->offset);
    }

    #[Test]
    public function toStringReturnsValue(): void
    {
        $id = new Identifier('Foo');

        self::assertSame('Foo', (string) $id);
        self::assertSame('Foo', $id->toString());
    }

    #[Test]
    public function toLowerStringReturnsLowercasedValue(): void
    {
        $id = new Identifier('FooBar');

        self::assertSame('foobar', $id->toLowerString());
    }

    #[Test]
    public function isVirtualIsTrueForHyphenatedName(): void
    {
        $id = new Identifier('non-empty-string');

        self::assertTrue($id->isVirtual);
    }

    #[Test]
    public function isVirtualIsFalseForNormalName(): void
    {
        $id = new Identifier('string');

        self::assertFalse($id->isVirtual);
    }

    #[Test]
    #[DataProvider('provideSpecialNames')]
    public function isSpecialIsTrueForSpecialClassNames(string $name): void
    {
        $id = new Identifier($name);

        self::assertTrue($id->isSpecial);
    }

    public static function provideSpecialNames(): iterable
    {
        return [['self'], ['parent'], ['static'], ['SELF'], ['PARENT'], ['Static']];
    }

    #[Test]
    public function isSpecialIsFalseForRegularName(): void
    {
        $id = new Identifier('MyClass');

        self::assertFalse($id->isSpecial);
    }

    #[Test]
    #[DataProvider('provideBuiltinNames')]
    public function isBuiltinIsTrueForBuiltinTypes(string $name): void
    {
        $id = new Identifier($name);

        self::assertTrue($id->isBuiltin);
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

        self::assertFalse($id->isBuiltin);
    }

    #[Test]
    public function createFromStringTrimsWhitespace(): void
    {
        $id = Identifier::createFromString('  Foo  ');

        self::assertSame('Foo', $id->value);
    }

    #[Test]
    public function createFromStringReturnsSameInstanceWhenPassedIdentifier(): void
    {
        $id = new Identifier('Foo');
        $result = Identifier::createFromString($id);

        self::assertSame($id, $result);
    }

    #[Test]
    public function createFromStringCreatesNewInstance(): void
    {
        $id = Identifier::createFromString('Bar');

        self::assertInstanceOf(Identifier::class, $id);
        self::assertSame('Bar', $id->value);
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
        self::assertTrue(Identifier::isLooksLikeSpecial('self'));
        self::assertTrue(Identifier::isLooksLikeSpecial('SELF'));
        self::assertTrue(Identifier::isLooksLikeSpecial('parent'));
        self::assertTrue(Identifier::isLooksLikeSpecial('static'));
    }

    #[Test]
    public function isLooksLikeSpecialReturnsFalseForOtherNames(): void
    {
        self::assertFalse(Identifier::isLooksLikeSpecial('Foo'));
        self::assertFalse(Identifier::isLooksLikeSpecial('int'));
    }

    #[Test]
    public function isLooksLikeBuiltinReturnsTrueForBuiltinNames(): void
    {
        self::assertTrue(Identifier::isLooksLikeBuiltin('int'));
        self::assertTrue(Identifier::isLooksLikeBuiltin('INT'));
        self::assertTrue(Identifier::isLooksLikeBuiltin('string'));
    }

    #[Test]
    public function isLooksLikeBuiltinReturnsFalseForCustomNames(): void
    {
        self::assertFalse(Identifier::isLooksLikeBuiltin('Foo'));
        self::assertFalse(Identifier::isLooksLikeBuiltin('self'));
    }

    #[Test]
    public function serializeAndUnserializeRoundtrip(): void
    {
        $id = new Identifier('MyClass');
        $id->offset = 42;

        /** @var Identifier $restored */
        $restored = \unserialize(\serialize($id));

        self::assertInstanceOf(Identifier::class, $restored);
        self::assertSame('MyClass', $restored->value);
        self::assertSame(42, $restored->offset);
    }

    #[Test]
    public function isVirtualMethodIsAnAliasOfProperty(): void
    {
        $id = new Identifier('non-empty-string');

        self::assertTrue($id->isVirtual());
        self::assertSame($id->isVirtual(), $id->isVirtual);
    }

    #[Test]
    public function isSpecialMethodIsAnAliasOfProperty(): void
    {
        $id = new Identifier('self');

        self::assertTrue($id->isSpecial());
        self::assertSame($id->isSpecial(), $id->isSpecial);
    }

    #[Test]
    public function isBuiltinMethodIsAnAliasOfProperty(): void
    {
        $id = new Identifier('int');

        self::assertTrue($id->isBuiltin());
        self::assertSame($id->isBuiltin(), $id->isBuiltin);
    }

    #[Test]
    public function issetReturnsTrueForVirtualProperties(): void
    {
        $id = new Identifier('Example');

        self::assertTrue(isset($id->isVirtual));
        self::assertTrue(isset($id->isSpecial));
        self::assertTrue(isset($id->isBuiltin));
    }

    #[Test]
    public function issetReturnsFalseForUnknownProperty(): void
    {
        $id = new Identifier('Example');

        self::assertFalse(isset($id->unknown));
    }

    #[Test]
    public function readingUnknownPropertyThrows(): void
    {
        $id = new Identifier('Example');

        $this->expectException(\OutOfRangeException::class);

        /** @phpstan-ignore-next-line */
        $id->unknown;
    }

    #[Test]
    public function specialNamesAreCaseInsensitive(): void
    {
        self::assertTrue((new Identifier('SELF'))->isSpecial());
        self::assertTrue((new Identifier('Parent'))->isSpecial());
        self::assertTrue(Identifier::isLooksLikeSpecial('StAtIc'));
    }

    #[Test]
    public function builtinNamesAreCaseInsensitive(): void
    {
        self::assertTrue((new Identifier('INT'))->isBuiltin());
        self::assertTrue((new Identifier('Iterable'))->isBuiltin());
        self::assertTrue(Identifier::isLooksLikeBuiltin('NuLl'));
    }

    #[Test]
    public function specialNameIsNotBuiltin(): void
    {
        $id = new Identifier('self');

        self::assertTrue($id->isSpecial());
        self::assertFalse($id->isBuiltin());
    }

    #[Test]
    public function builtinNameIsNotSpecial(): void
    {
        $id = new Identifier('string');

        self::assertTrue($id->isBuiltin());
        self::assertFalse($id->isSpecial());
    }

    #[Test]
    public function virtualNameIsDetectedInAnyPosition(): void
    {
        self::assertTrue((new Identifier('-leading'))->isVirtual());
        self::assertTrue((new Identifier('trailing-'))->isVirtual());
        self::assertTrue((new Identifier('a-b-c'))->isVirtual());
    }

    #[Test]
    public function underscoredNameIsNotVirtual(): void
    {
        self::assertFalse((new Identifier('non_empty_string'))->isVirtual());
    }

    #[Test]
    public function createFromStringAcceptsStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Example';
            }
        };

        self::assertSame('Example', Identifier::createFromString($stringable)->value);
    }

    #[Test]
    public function createFromStringTrimsAllSurroundingWhitespaceKinds(): void
    {
        self::assertSame('Example', Identifier::createFromString("\n\t Example \r\n")->value);
    }

    #[Test]
    public function createFromStringThrowsOnStringableReturningEmptyString(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return '   ';
            }
        };

        $this->expectException(\InvalidArgumentException::class);

        Identifier::createFromString($stringable);
    }

    #[Test]
    public function offsetMethodIsAnAliasOfProperty(): void
    {
        $id = new Identifier('Example');
        $id->offset = 17;

        self::assertSame(17, $id->offset());
    }

    #[Test]
    public function serializePayloadContainsOffsetAndValue(): void
    {
        $id = new Identifier('Example');
        $id->offset = 5;

        self::assertSame([5, 'Example'], $id->__serialize());
    }

    #[Test]
    public function serializationRoundtripPreservesOffset(): void
    {
        $id = new Identifier('Example');
        $id->offset = 5;

        /** @var Identifier $restored */
        $restored = \unserialize(\serialize($id));

        self::assertSame(5, $restored->offset());
        self::assertSame('Example', $restored->value);
    }

    #[Test]
    public function unserializeThrowsWhenOffsetIsMissing(): void
    {
        $id = new Identifier('Example');

        $this->expectException(\UnexpectedValueException::class);

        $id->__unserialize([]);
    }

    #[Test]
    public function unserializeThrowsWhenValueIsMissing(): void
    {
        $id = new Identifier('Example');

        $this->expectException(\UnexpectedValueException::class);

        $id->__unserialize([0]);
    }

    #[Test]
    public function stringCastIsTheSameAsToStringMethod(): void
    {
        $id = new Identifier('Example');

        self::assertSame($id->toString(), (string) $id);
    }

    #[Test]
    public function toLowerStringDoesNotModifyTheValue(): void
    {
        $id = new Identifier('SomeClass');

        self::assertSame('someclass', $id->toLowerString());
        self::assertSame('SomeClass', $id->value);
    }
}
