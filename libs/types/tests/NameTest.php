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

        $this->assertCount(1, $name);
        $this->assertSame('Foo', (string) $name);
    }

    #[Test]
    public function constructorWithMultipleSegments(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        $this->assertCount(2, $name);
        $this->assertSame('Foo\Bar', $name->toString());
    }

    #[Test]
    public function defaultIsNotFullyQualified(): void
    {
        $name = new Name([$this->id('Foo')]);

        $this->assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function fullyQualifiedFlagIsStored(): void
    {
        $name = new Name([$this->id('Foo')], true);

        $this->assertTrue($name->isFullyQualified);
    }

    #[Test]
    public function firstPropertyReturnsFirstSegment(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        $this->assertSame('Foo', $name->first->value);
    }

    #[Test]
    public function lastPropertyReturnsLastSegment(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        $this->assertSame('Bar', $name->last->value);
    }

    #[Test]
    public function isSimpleIsTrueForSingleSegment(): void
    {
        $name = new Name([$this->id('Foo')]);

        $this->assertTrue($name->isSimple);
    }

    #[Test]
    public function isSimpleIsFalseForMultipleSegments(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        $this->assertFalse($name->isSimple);
    }

    #[Test]
    public function isSpecialIsTrueForSpecialSingleSegment(): void
    {
        $name = new Name([$this->id('self')]);

        $this->assertTrue($name->isSpecial);
    }

    #[Test]
    public function isSpecialIsFalseForMultiSegmentNameStartingWithSpecial(): void
    {
        $name = new Name([$this->id('self'), $this->id('Foo')]);

        $this->assertFalse($name->isSpecial);
    }

    #[Test]
    public function isBuiltinIsTrueForBuiltinSingleSegment(): void
    {
        $name = new Name([$this->id('int')]);

        $this->assertTrue($name->isBuiltin);
    }

    #[Test]
    public function isBuiltinIsFalseForMultiSegmentName(): void
    {
        $name = new Name([$this->id('int'), $this->id('Foo')]);

        $this->assertFalse($name->isBuiltin);
    }

    #[Test]
    public function createFromStringParsesSimpleName(): void
    {
        $name = Name::createFromString('Foo');

        $this->assertSame('Foo', $name->toString());
        $this->assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function createFromStringParsesQualifiedName(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        $this->assertSame('Foo\Bar\Baz', $name->toString());
        $this->assertCount(3, $name);
    }

    #[Test]
    public function createFromStringDetectsFullyQualified(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        $this->assertTrue($name->isFullyQualified);
        $this->assertSame('\Foo\Bar', $name->toString());
    }

    #[Test]
    public function createFromStringSegments(): void
    {
        $name = Name::createFromStringSegments(['Foo', 'Bar']);

        $this->assertSame('Foo\Bar', $name->toString());
    }

    #[Test]
    public function sliceReturnsSubName(): void
    {
        $name = Name::createFromString('A\B\C');
        $sliced = $name->slice(1);

        $this->assertSame('B\C', $sliced->toString());
    }

    #[Test]
    public function sliceWithLength(): void
    {
        $name = Name::createFromString('A\B\C');
        $sliced = $name->slice(0, 2);

        $this->assertSame('A\B', $sliced->toString());
    }

    #[Test]
    public function withAddedAppendsSegments(): void
    {
        $a = Name::createFromString('Some\Any');
        $b = Name::createFromString('Test\Class');

        $result = $a->withAdded($b);

        $this->assertSame('Some\Any\Test\Class', $result->toString());
    }

    #[Test]
    public function mergeWithDropsFirstSegmentOfAdded(): void
    {
        $name = Name::createFromString('Some\Any');
        $alias = Name::createFromString('Any\Class');

        $result = $name->mergeWith($alias);

        $this->assertSame('Some\Any\Class', $result->toString());
    }

    #[Test]
    public function toFullQualifiedConvertsName(): void
    {
        $name = Name::createFromString('Foo\Bar');
        $fq = $name->toFullQualified();

        $this->assertTrue($fq->isFullyQualified);
        $this->assertSame('\Foo\Bar', $fq->toString());
    }

    #[Test]
    public function toFullQualifiedReturnsCloneIfAlreadyFullyQualified(): void
    {
        $name = Name::createFromString('\Foo\Bar');
        $fq = $name->toFullQualified();

        $this->assertTrue($fq->isFullyQualified);
    }

    #[Test]
    public function toUnqualifiedConvertsName(): void
    {
        $name = Name::createFromString('\Foo\Bar');
        $uq = $name->toUnqualified();

        $this->assertFalse($uq->isFullyQualified);
        $this->assertSame('Foo\Bar', $uq->toString());
    }

    #[Test]
    public function toStringArrayReturnsSegmentStrings(): void
    {
        $name = Name::createFromString('A\B\C');

        $this->assertSame(['A', 'B', 'C'], $name->toStringArray());
    }

    #[Test]
    public function toLowerStringArrayReturnsLowercasedSegments(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $this->assertSame(['foo', 'bar'], $name->toLowercaseStringArray());
    }

    #[Test]
    public function toUnqualifiedStringDoesNotIncludeLeadingBackslash(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        $this->assertSame('Foo\Bar', $name->toUnqualifiedString());
    }

    #[Test]
    public function toFullQualifiedStringIncludesLeadingBackslash(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $this->assertSame('\Foo\Bar', $name->toFullQualifiedString());
    }

    #[Test]
    public function iteratorYieldsSegments(): void
    {
        $name = Name::createFromString('A\B');
        $collected = \iterator_to_array($name);

        $this->assertCount(2, $collected);
        $this->assertSame('A', $collected[0]->value);
        $this->assertSame('B', $collected[1]->value);
    }

    #[Test]
    public function countReturnsNumberOfSegments(): void
    {
        $name = Name::createFromString('A\B\C');

        $this->assertSame(3, $name->count());
    }

    #[Test]
    public function toLowerStringReturnsLowercasedName(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $this->assertSame('foo\bar', $name->toLowerString());
    }

    #[Test]
    public function serializeAndUnserializeRoundtrip(): void
    {
        $name = Name::createFromString('Foo\Bar');
        $name->offset = 10;

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        $this->assertInstanceOf(Name::class, $restored);
        $this->assertSame(['Foo', 'Bar'], $restored->toStringArray());
        $this->assertSame(10, $restored->offset);
    }

    #[Test]
    public function constructorThrowsOnEmptySegmentsArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Name([]);
    }
}
