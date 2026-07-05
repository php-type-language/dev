<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

final class NameTest extends TestCase
{
    private function id(string $v): Identifier
    {
        return new Identifier($v);
    }

    #[Test]
    public function constructorWithSingleSegment(): void
    {
        $name = new Name([$this->id('Foo')]);

        self::assertCount(1, $name);
        self::assertSame('Foo', (string) $name);
    }

    #[Test]
    public function constructorWithMultipleSegments(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertCount(2, $name);
        self::assertSame('Foo\Bar', $name->toString());
    }

    #[Test]
    public function defaultIsNotFullyQualified(): void
    {
        $name = new Name([$this->id('Foo')]);

        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function fullyQualifiedFlagIsStored(): void
    {
        $name = new Name([$this->id('Foo')], true);

        self::assertTrue($name->isFullyQualified);
    }

    #[Test]
    public function firstPropertyReturnsFirstSegment(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertSame('Foo', $name->first->value);
    }

    #[Test]
    public function lastPropertyReturnsLastSegment(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertSame('Bar', $name->last->value);
    }

    #[Test]
    public function isSimpleIsTrueForSingleSegment(): void
    {
        $name = new Name([$this->id('Foo')]);

        self::assertTrue($name->isSimple);
    }

    #[Test]
    public function isSimpleIsFalseForMultipleSegments(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertFalse($name->isSimple);
    }

    #[Test]
    public function isSpecialIsTrueForSpecialSingleSegment(): void
    {
        $name = new Name([$this->id('self')]);

        self::assertTrue($name->isSpecial);
    }

    #[Test]
    public function isSpecialIsFalseForMultiSegmentNameStartingWithSpecial(): void
    {
        $name = new Name([$this->id('self'), $this->id('Foo')]);

        self::assertFalse($name->isSpecial);
    }

    #[Test]
    public function isBuiltinIsTrueForBuiltinSingleSegment(): void
    {
        $name = new Name([$this->id('int')]);

        self::assertTrue($name->isBuiltin);
    }

    #[Test]
    public function isBuiltinIsFalseForMultiSegmentName(): void
    {
        $name = new Name([$this->id('int'), $this->id('Foo')]);

        self::assertFalse($name->isBuiltin);
    }

    #[Test]
    public function createFromStringParsesSimpleName(): void
    {
        $name = Name::createFromString('Foo');

        self::assertSame('Foo', $name->toString());
        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function createFromStringParsesQualifiedName(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        self::assertSame('Foo\Bar\Baz', $name->toString());
        self::assertCount(3, $name);
    }

    #[Test]
    public function createFromStringDetectsFullyQualified(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        self::assertTrue($name->isFullyQualified);
        self::assertSame('\Foo\Bar', $name->toString());
    }

    #[Test]
    public function createFromStringSegments(): void
    {
        $name = Name::createFromStringSegments(['Foo', 'Bar']);

        self::assertSame('Foo\Bar', $name->toString());
    }

    #[Test]
    public function sliceReturnsSubName(): void
    {
        $name = Name::createFromString('A\B\C');
        $sliced = $name->slice(1);

        self::assertSame('B\C', $sliced->toString());
    }

    #[Test]
    public function sliceWithLength(): void
    {
        $name = Name::createFromString('A\B\C');
        $sliced = $name->slice(0, 2);

        self::assertSame('A\B', $sliced->toString());
    }

    #[Test]
    public function withAddedAppendsSegments(): void
    {
        $a = Name::createFromString('Some\Any');
        $b = Name::createFromString('Test\Class');

        $result = $a->withAdded($b);

        self::assertSame('Some\Any\Test\Class', $result->toString());
    }

    #[Test]
    public function mergeWithDropsFirstSegmentOfAdded(): void
    {
        $name = Name::createFromString('Some\Any');
        $alias = Name::createFromString('Any\Class');

        $result = $name->mergeWith($alias);

        self::assertSame('Some\Any\Class', $result->toString());
    }

    #[Test]
    public function toFullQualifiedConvertsName(): void
    {
        $name = Name::createFromString('Foo\Bar');
        $fq = $name->toFullQualified();

        self::assertTrue($fq->isFullyQualified);
        self::assertSame('\Foo\Bar', $fq->toString());
    }

    #[Test]
    public function toFullQualifiedReturnsCloneIfAlreadyFullyQualified(): void
    {
        $name = Name::createFromString('\Foo\Bar');
        $fq = $name->toFullQualified();

        self::assertTrue($fq->isFullyQualified);
    }

    #[Test]
    public function toUnqualifiedConvertsName(): void
    {
        $name = Name::createFromString('\Foo\Bar');
        $uq = $name->toUnqualified();

        self::assertFalse($uq->isFullyQualified);
        self::assertSame('Foo\Bar', $uq->toString());
    }

    #[Test]
    public function toStringArrayReturnsSegmentStrings(): void
    {
        $name = Name::createFromString('A\B\C');

        self::assertSame(['A', 'B', 'C'], $name->toStringArray());
    }

    #[Test]
    public function toLowerStringArrayReturnsLowercasedSegments(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame(['foo', 'bar'], $name->toLowercaseStringArray());
    }

    #[Test]
    public function toUnqualifiedStringDoesNotIncludeLeadingBackslash(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        self::assertSame('Foo\Bar', $name->toUnqualifiedString());
    }

    #[Test]
    public function toFullQualifiedStringIncludesLeadingBackslash(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame('\Foo\Bar', $name->toFullQualifiedString());
    }

    #[Test]
    public function iteratorYieldsSegments(): void
    {
        $name = Name::createFromString('A\B');
        $collected = \iterator_to_array($name);

        self::assertCount(2, $collected);
        self::assertSame('A', $collected[0]->value);
        self::assertSame('B', $collected[1]->value);
    }

    #[Test]
    public function countReturnsNumberOfSegments(): void
    {
        $name = Name::createFromString('A\B\C');

        self::assertSame(3, $name->count());
    }

    #[Test]
    public function toLowerStringReturnsLowercasedName(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame('foo\bar', $name->toLowerString());
    }

    #[Test]
    public function serializeAndUnserializeRoundtrip(): void
    {
        $name = Name::createFromString('Foo\Bar');
        $name->offset = 10;

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertInstanceOf(Name::class, $restored);
        self::assertSame(['Foo', 'Bar'], $restored->toStringArray());
        self::assertSame(10, $restored->offset);
    }

    #[Test]
    public function constructorThrowsOnEmptySegmentsArray(): void
    {
        self::skipWhenAssertsAreDisabled();

        $this->expectException(\InvalidArgumentException::class);

        new Name([]);
    }
}
