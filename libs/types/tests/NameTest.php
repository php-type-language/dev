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

        self::assertSame('Foo', $name->getFirstPart()->value);
    }

    #[Test]
    public function lastPropertyReturnsLastSegment(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertSame('Bar', $name->getLastPart()->value);
    }

    #[Test]
    public function isSimpleIsTrueForSingleSegment(): void
    {
        $name = new Name([$this->id('Foo')]);

        self::assertTrue($name->isSimple());
    }

    #[Test]
    public function isSimpleIsFalseForMultipleSegments(): void
    {
        $name = new Name([$this->id('Foo'), $this->id('Bar')]);

        self::assertFalse($name->isSimple());
    }

    #[Test]
    public function isSpecialIsTrueForSpecialSingleSegment(): void
    {
        $name = new Name([$this->id('self')]);

        self::assertTrue($name->isSpecial());
    }

    #[Test]
    public function isSpecialIsFalseForMultiSegmentNameStartingWithSpecial(): void
    {
        $name = new Name([$this->id('self'), $this->id('Foo')]);

        self::assertFalse($name->isSpecial());
    }

    #[Test]
    public function isBuiltinIsTrueForBuiltinSingleSegment(): void
    {
        $name = new Name([$this->id('int')]);

        self::assertTrue($name->isBuiltin());
    }

    #[Test]
    public function isBuiltinIsFalseForMultiSegmentName(): void
    {
        $name = new Name([$this->id('int'), $this->id('Foo')]);

        self::assertFalse($name->isBuiltin());
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

        self::assertSame(['A', 'B', 'C'], $name->toArrayStrings());
    }

    #[Test]
    public function toLowerStringArrayReturnsLowercasedSegments(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame(['foo', 'bar'], $name->toArrayLowercaseStrings());
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
        self::assertSame(['Foo', 'Bar'], $restored->toArrayStrings());
        self::assertSame(10, $restored->offset);
    }

    #[Test]
    public function constructorThrowsOnEmptySegmentsArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Name([]);
    }

    #[Test]
    public function fullyQualifiedDefaultValueConstantIsFalse(): void
    {
        self::assertFalse(Name::IS_FULLY_QUALIFIED_DEFAULT_VALUE);
    }

    #[Test]
    public function toUnqualifiedLowerStringLowercasesNameWithoutLeadingDelimiter(): void
    {
        $name = Name::createFromString('\Vendor\Package\SomeClass');

        self::assertSame('vendor\package\someclass', $name->toUnqualifiedLowerString());
    }

    #[Test]
    public function toFullQualifiedLowerStringKeepsLeadingDelimiter(): void
    {
        $name = Name::createFromString('\Vendor\Package\SomeClass');

        self::assertSame('\vendor\package\someclass', $name->toFullQualifiedLowerString());
    }

    #[Test]
    public function toFullQualifiedStringIsIndependentOfTheQualificationFlag(): void
    {
        $name = Name::createFromString('Vendor\SomeClass');

        self::assertFalse($name->isFullyQualified);
        self::assertSame('\Vendor\SomeClass', $name->toFullQualifiedString());
        self::assertSame('\vendor\someclass', $name->toFullQualifiedLowerString());
    }

    #[Test]
    public function toStringDependsOnTheQualificationFlag(): void
    {
        $unqualified = new Name([$this->id('Foo')], false);
        $qualified = new Name([$this->id('Foo')], true);

        self::assertSame('Foo', $unqualified->toString());
        self::assertSame('\Foo', $qualified->toString());
    }

    #[Test]
    public function createFromStringIgnoresRepeatedDelimiters(): void
    {
        $name = Name::createFromString('Foo\\\\Bar');

        self::assertSame(['Foo', 'Bar'], $name->toArrayStrings());
    }

    #[Test]
    public function createFromStringIgnoresTrailingDelimiter(): void
    {
        $name = Name::createFromString('Foo\Bar\\');

        self::assertSame(['Foo', 'Bar'], $name->toArrayStrings());
        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function createFromStringAcceptsStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Foo\Bar';
            }
        };

        self::assertSame(['Foo', 'Bar'], Name::createFromString($stringable)->toArrayStrings());
    }

    #[Test]
    public function createFromStringThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Name::createFromString('');
    }

    #[Test]
    public function createFromStringThrowsOnDelimiterOnlyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Name::createFromString('\\');
    }

    #[Test]
    public function sliceKeepsQualificationFlag(): void
    {
        $name = Name::createFromString('\Foo\Bar\Baz');

        $sliced = $name->slice(1);

        self::assertSame(['Bar', 'Baz'], $sliced->toArrayStrings());
        self::assertTrue($sliced->isFullyQualified);
    }

    #[Test]
    public function sliceDoesNotModifyTheOriginalName(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        $name->slice(1);

        self::assertSame(['Foo', 'Bar', 'Baz'], $name->toArrayStrings());
    }

    #[Test]
    public function withAddedKeepsQualificationFlagOfTheReceiver(): void
    {
        $name = Name::createFromString('\Some\Any');

        $result = $name->withAdded(Name::createFromString('Test\Class'));

        self::assertSame(['Some', 'Any', 'Test', 'Class'], $result->toArrayStrings());
        self::assertTrue($result->isFullyQualified);
    }

    #[Test]
    public function withAddedDoesNotModifyArguments(): void
    {
        $name = Name::createFromString('Some\Any');
        $added = Name::createFromString('Test');

        $name->withAdded($added);

        self::assertSame(['Some', 'Any'], $name->toArrayStrings());
        self::assertSame(['Test'], $added->toArrayStrings());
    }

    #[Test]
    public function mergeWithReplacesTheAliasSegment(): void
    {
        $name = Name::createFromString('TypeLang\Parser\Exception');

        $result = $name->mergeWith(Name::createFromString('Error\SemanticException'));

        self::assertSame('TypeLang\Parser\Exception\SemanticException', $result->toString());
    }

    #[Test]
    public function mergeWithSimpleNameReturnsTheReceiverSegments(): void
    {
        $name = Name::createFromString('TypeLang\Parser\Node');

        $result = $name->mergeWith(Name::createFromString('Node'));

        self::assertSame('TypeLang\Parser\Node', $result->toString());
    }

    #[Test]
    public function toFullQualifiedKeepsSegments(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $result = $name->toFullQualified();

        self::assertTrue($result->isFullyQualified);
        self::assertSame(['Foo', 'Bar'], $result->toArrayStrings());
        self::assertFalse($name->isFullyQualified, 'The original name must not be modified');
    }

    #[Test]
    public function toUnqualifiedKeepsSegments(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        $result = $name->toUnqualified();

        self::assertFalse($result->isFullyQualified);
        self::assertSame(['Foo', 'Bar'], $result->toArrayStrings());
        self::assertTrue($name->isFullyQualified, 'The original name must not be modified');
    }

    #[Test]
    public function toUnqualifiedOfUnqualifiedNameReturnsEqualName(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $result = $name->toUnqualified();

        self::assertFalse($result->isFullyQualified);
        self::assertSame('Foo\Bar', $result->toString());
    }

    #[Test]
    public function serializationRoundtripPreservesQualificationFlag(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertTrue($restored->isFullyQualified);
        self::assertSame('\Foo\Bar', $restored->toString());
    }

    #[Test]
    public function serializePayloadContainsSegmentsOffsetAndFqnFlag(): void
    {
        $name = Name::createFromString('Foo');
        $name->offset = 3;

        self::assertSame([$name->parts, 3, false], $name->__serialize());
    }

    #[Test]
    public function unserializeThrowsWhenSegmentsAreMissing(): void
    {
        $name = Name::createFromString('Foo');

        $this->expectException(\UnexpectedValueException::class);

        $name->__unserialize([]);
    }

    #[Test]
    public function offsetMethodIsAnAliasOfProperty(): void
    {
        $name = Name::createFromString('Foo');
        $name->offset = 9;

        self::assertSame(9, $name->offset);
    }

    #[Test]
    public function stringCastIsTheSameAsToStringMethod(): void
    {
        $name = Name::createFromString('\Foo\Bar');

        self::assertSame($name->toString(), (string) $name);
    }

    #[Test]
    public function getFirstPartReturnsTheFirstSegment(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        self::assertSame($name->first, $name->getFirstPart());
        self::assertSame('Foo', $name->getFirstPart()->value);
    }

    #[Test]
    public function getFirstPartAsStringReturnsTheFirstSegmentValue(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame('Foo', $name->getFirstPartAsString());
    }

    #[Test]
    public function getFirstPartAsLowerStringLowercasesTheFirstSegment(): void
    {
        $name = Name::createFromString('FooBar\Baz');

        self::assertSame('foobar', $name->getFirstPartAsLowerString());
    }

    #[Test]
    public function getLastPartReturnsTheLastSegment(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        self::assertSame($name->last, $name->getLastPart());
        self::assertSame('Baz', $name->getLastPart()->value);
    }

    #[Test]
    public function getLastPartAsStringReturnsTheLastSegmentValue(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame('Bar', $name->getLastPartAsString());
    }

    #[Test]
    public function getLastPartAsLowerStringLowercasesTheLastSegment(): void
    {
        $name = Name::createFromString('Foo\BarBaz');

        self::assertSame('barbaz', $name->getLastPartAsLowerString());
    }

    #[Test]
    public function partsOfASimpleNameAreTheSameSegment(): void
    {
        $name = Name::createFromString('Foo');

        self::assertSame($name->first, $name->last);
    }

    #[Test]
    public function toArrayReturnsTheSegments(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame($name->parts, $name->toArray());
        self::assertContainsOnlyInstancesOf(Identifier::class, $name->toArray());
    }

    #[Test]
    public function isFullQualifiedIsAnAliasOfTheProperty(): void
    {
        $qualified = Name::createFromString('\Foo\Bar');
        $unqualified = Name::createFromString('Foo\Bar');

        self::assertTrue($qualified->isFullQualified());
        self::assertFalse($unqualified->isFullQualified());
    }

    #[Test]
    public function getPartsIsAnAliasOfTheProperty(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame($name->parts, $name->getParts());
    }

    #[Test]
    public function getPartsAsStringIsAnAliasOfToArrayStrings(): void
    {
        $name = Name::createFromString('Foo\Bar');

        self::assertSame(['Foo', 'Bar'], $name->getPartsAsString());
        self::assertSame($name->toArrayStrings(), $name->getPartsAsString());
    }

    #[Test]
    public function serializationRoundtripRestoresBoundaryParts(): void
    {
        $name = Name::createFromString('Foo\Bar\Baz');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertSame('Foo', $restored->first->value);
        self::assertSame('Baz', $restored->last->value);
        self::assertSame('Foo', $restored->getFirstPart()->value);
        self::assertSame('Baz', $restored->getLastPart()->value);
    }

    #[Test]
    public function unserializedNameIsFullyUsable(): void
    {
        $name = Name::createFromString('self');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertTrue($restored->isSimple());
        self::assertTrue($restored->isSpecial());
        self::assertFalse($restored->isBuiltin());
        self::assertSame('self', $restored->getFirstPartAsString());
    }

    #[Test]
    public function unserializeThrowsOnEmptyParts(): void
    {
        $this->expectException(\Throwable::class);

        \unserialize(self::payload([], 0, false));
    }

    #[Test]
    public function unserializeThrowsOnNonIdentifierParts(): void
    {
        $this->expectException(\Throwable::class);

        \unserialize(self::payload(['Foo'], 0, false));
    }

    #[Test]
    public function unserializeThrowsOnMissingParts(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        \unserialize(self::payload());
    }

    /**
     * Builds a serialized {@see Name} payload from the given
     * {@see Name::__serialize()} data.
     *
     * @return non-empty-string
     */
    private static function payload(mixed ...$data): string
    {
        $body = \serialize($data);

        return \vsprintf('O:%d:"%s":%d:%s', [
            \strlen(Name::class),
            Name::class,
            \count($data),
            \substr($body, (int) \strpos($body, '{')),
        ]);
    }

    #[Test]
    public function constructorThrowsOnNonIdentifierParts(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @phpstan-ignore-next-line */
        new Name(['Foo']);
    }

    #[Test]
    public function sliceThrowsWhenTheResultIsEmpty(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $this->expectException(\InvalidArgumentException::class);

        $name->slice(5);
    }

    #[Test]
    public function sliceOfZeroLengthThrows(): void
    {
        $name = Name::createFromString('Foo\Bar');

        $this->expectException(\InvalidArgumentException::class);

        $name->slice(0, 0);
    }
}
