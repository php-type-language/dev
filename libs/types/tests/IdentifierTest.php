<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;

final class IdentifierTest extends TestCase
{

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

        self::assertTrue($id->isVirtual());
    }

    #[Test]
    public function isVirtualIsFalseForNormalName(): void
    {
        $id = new Identifier('string');

        self::assertFalse($id->isVirtual());
    }

    #[Test]
    #[DataProvider('specialNamesDataProvider')]
    public function isSpecialIsTrueForSpecialClassNames(string $name): void
    {
        $id = new Identifier($name);

        self::assertTrue($id->isSpecial());
    }

    public static function specialNamesDataProvider(): iterable
    {
        return [['self'], ['parent'], ['static'], ['SELF'], ['PARENT'], ['Static']];
    }

    #[Test]
    public function isSpecialIsFalseForRegularName(): void
    {
        $id = new Identifier('MyClass');

        self::assertFalse($id->isSpecial());
    }

    #[Test]
    #[DataProvider('builtinNamesDataProvider')]
    public function isBuiltinIsTrueForBuiltinTypes(string $name): void
    {
        $id = new Identifier($name);

        self::assertTrue($id->isBuiltin());
    }

    public static function builtinNamesDataProvider(): iterable
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

        self::assertFalse($id->isBuiltin());
    }

    #[Test]
    public function fromTrimsWhitespace(): void
    {
        $id = Identifier::from('  Foo  ');

        self::assertSame('Foo', $id->value);
    }

    #[Test]
    public function fromCopiesAnIdentifierWithAnotherOffset(): void
    {
        $id = new Identifier('Foo', 3);
        $copy = Identifier::from($id, 9);

        self::assertNotSame($id, $copy);
        self::assertSame('Foo', $copy->value);
        self::assertSame(9, $copy->offset);
    }

    #[Test]
    public function fromReturnsSameInstanceWhenPassedIdentifier(): void
    {
        $id = new Identifier('Foo');
        $result = Identifier::from($id);

        self::assertSame($id, $result);
    }

    #[Test]
    public function fromThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Identifier::from('');
    }

    #[Test]
    public function fromThrowsOnWhitespaceOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Identifier::from('   ');
    }

    #[Test]
    public function specialNamesAreCaseInsensitive(): void
    {
        self::assertTrue((new Identifier('SELF'))->isSpecial());
        self::assertTrue((new Identifier('Parent'))->isSpecial());
        self::assertTrue(Identifier::isSpecialClassName('StAtIc'));
    }

    #[Test]
    public function builtinNamesAreCaseInsensitive(): void
    {
        self::assertTrue((new Identifier('INT'))->isBuiltin());
        self::assertTrue((new Identifier('Iterable'))->isBuiltin());
        self::assertTrue(Identifier::isBuiltinTypeName('NuLl'));
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
    public function fromAcceptsStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Example';
            }
        };

        self::assertSame('Example', Identifier::from($stringable)->value);
    }

    #[Test]
    public function fromTrimsAllSurroundingWhitespaceKinds(): void
    {
        self::assertSame('Example', Identifier::from("\n\t Example \r\n")->value);
    }

    #[Test]
    public function fromThrowsOnStringableReturningEmptyString(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return '   ';
            }
        };

        $this->expectException(\InvalidArgumentException::class);

        Identifier::from($stringable);
    }

    #[Test]
    public function constructorThrowsOnEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Identifier('');
    }

    #[Test]
    public function constructorKeepsSurroundingWhitespace(): void
    {
        $id = new Identifier(' A ');

        self::assertSame(' A ', $id->value);
    }
}
