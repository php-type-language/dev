<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;

final class NameTest extends TestCase
{
    #[Test]
    public function valueAndOffsetAreKept(): void
    {
        $name = new Name('Some\Any', 7);

        self::assertSame('Some\Any', $name->unqualified);
        self::assertSame('Some\Any', $name->toString());
        self::assertSame('Some\Any', (string) $name);
        self::assertSame(7, $name->offset);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, bool, non-empty-list<non-empty-string>}>
     */
    public static function segmentsDataProvider(): iterable
    {
        yield 'simple' => ['Foo', false, ['Foo']];
        yield 'qualified' => ['Foo\Bar', false, ['Foo', 'Bar']];
        yield 'fully qualified' => ['\Foo', true, ['Foo']];
        yield 'fully qualified and qualified' => ['\Foo\Bar\Baz', true, ['Foo', 'Bar', 'Baz']];
        yield 'hyphen' => ['non-empty-string', false, ['non-empty-string']];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-list<non-empty-string> $segments
     */
    #[Test]
    #[DataProvider('segmentsDataProvider')]
    public function segmentsAreSplitOutOfTheValue(string $value, bool $isFullyQualified, array $segments): void
    {
        $name = new Name($value);

        self::assertSame($isFullyQualified, $name->isFullyQualified);
        self::assertSame($segments, $name->toArray());
        self::assertSame($segments, \iterator_to_array($name));
        self::assertCount(\count($segments), $name);
        self::assertSame(\count($segments) === 1, $name->isSimple());
    }

    #[Test]
    public function firstAndLastAreTheEdgesOfTheName(): void
    {
        $name = new Name('\Some\Any\Test');

        self::assertSame('Some', $name->first());
        self::assertSame('Test', $name->last());
    }

    #[Test]
    public function firstAndLastOfASimpleNameAreTheSame(): void
    {
        $name = new Name('Some');

        self::assertSame('Some', $name->first());
        self::assertSame('Some', $name->last());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, bool}>
     */
    public static function specialDataProvider(): iterable
    {
        yield 'self' => ['self', true];
        yield 'parent' => ['parent', true];
        yield 'static' => ['static', true];
        yield 'in upper case' => ['SELF', true];
        yield 'other word' => ['int', false];
        yield 'qualified' => ['Foo\self', false];
    }

    /**
     * @param non-empty-string $value
     */
    #[Test]
    #[DataProvider('specialDataProvider')]
    public function specialNameIsRecognized(string $value, bool $expected): void
    {
        self::assertSame($expected, (new Name($value))->isSpecial());
    }

    #[Test]
    public function virtualNameHasAHyphen(): void
    {
        self::assertTrue((new Name('non-empty-string'))->isVirtual());
        self::assertFalse((new Name('Some\Any'))->isVirtual());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function fullyQualifiedDataProvider(): iterable
    {
        yield 'simple' => ['Foo', '\Foo'];
        yield 'qualified' => ['Foo\Bar', '\Foo\Bar'];
        yield 'already fully qualified' => ['\Foo\Bar', '\Foo\Bar'];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('fullyQualifiedDataProvider')]
    public function toFullyQualifiedAddsTheLeadingSeparator(string $value, string $expected): void
    {
        $name = (new Name($value, 5))->toFullyQualified();

        self::assertSame($expected, $name->toString());
        self::assertTrue($name->isFullyQualified);
        self::assertSame(5, $name->offset);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function unqualifiedDataProvider(): iterable
    {
        yield 'simple' => ['\Foo', 'Foo'];
        yield 'qualified' => ['\Foo\Bar', 'Foo\Bar'];
        yield 'already unqualified' => ['Foo\Bar', 'Foo\Bar'];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('unqualifiedDataProvider')]
    public function toUnqualifiedDropsTheLeadingSeparator(string $value, string $expected): void
    {
        $name = (new Name($value, 5))->toUnqualified();

        self::assertSame($expected, $name->unqualified);
        self::assertFalse($name->isFullyQualified);
        self::assertSame(5, $name->offset);
    }

    #[Test]
    public function qualificationKeepsTheNameWhenThereIsNothingToChange(): void
    {
        $relative = new Name('Foo', 3);
        $absolute = new Name('\Foo', 3);

        self::assertEquals($relative, $relative->toUnqualified());
        self::assertEquals($absolute, $absolute->toFullyQualified());
    }

    #[Test]
    public function leadingSeparatorIsKeptApartFromTheValue(): void
    {
        $name = new Name('\Some\Any');

        self::assertSame('Some\Any', $name->unqualified);
        self::assertSame('\Some\Any', $name->toString());
        self::assertSame('\Some\Any', (string) $name);
        self::assertTrue($name->isFullyQualified);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function onlySeparatorsDataProvider(): iterable
    {
        yield 'one' => ['\\'];
        yield 'two' => ['\\\\'];
        yield 'three' => ['\\\\\\'];
    }

    /**
     * @param non-empty-string $value
     */
    #[Test]
    #[DataProvider('onlySeparatorsDataProvider')]
    public function nameWithNothingButSeparatorsIsRejected(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Name($value);
    }

    #[Test]
    public function offsetIsZeroByDefault(): void
    {
        self::assertSame(0, (new Name('Foo'))->offset);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, bool}>
     */
    public static function surroundingSeparatorsDataProvider(): iterable
    {
        yield 'trailing' => ['Foo\\', 'Foo', false];
        yield 'leading and trailing' => ['\\Foo\\', 'Foo', true];
        yield 'doubled leading' => ['\\\\Foo', 'Foo', true];
        yield 'doubled trailing' => ['Foo\\\\', 'Foo', false];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('surroundingSeparatorsDataProvider')]
    public function separatorsAroundTheNameAreDropped(string $value, string $expected, bool $isFullyQualified): void
    {
        $name = new Name($value);

        self::assertSame($expected, $name->unqualified);
        self::assertSame($isFullyQualified, $name->isFullyQualified);
    }

    #[Test]
    public function emptySegmentsInsideAreSkippedInTheSegments(): void
    {
        $name = new Name('Foo\\\\Bar');

        self::assertSame(['Foo', 'Bar'], $name->toArray());
        self::assertCount(2, $name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function lowerStringDataProvider(): iterable
    {
        yield 'simple' => ['FOO', 'foo'];
        yield 'qualified' => ['Some\ANY', 'some\any'];
        yield 'fully qualified' => ['\Some\Any', '\some\any'];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('lowerStringDataProvider')]
    public function toLowerStringLowersTheWholeName(string $value, string $expected): void
    {
        self::assertSame($expected, (new Name($value))->toLowerString());
    }

    #[Test]
    public function hyphenInAnySegmentMakesTheNameVirtual(): void
    {
        self::assertTrue((new Name('Some\non-empty'))->isVirtual());
        self::assertTrue((new Name('\non-empty\Any'))->isVirtual());
    }

    #[Test]
    public function nameIsStringableCountableAndIterable(): void
    {
        $name = new Name('Some\Any');

        self::assertInstanceOf(\Stringable::class, $name);
        self::assertInstanceOf(\Countable::class, $name);
        self::assertInstanceOf(\IteratorAggregate::class, $name);
    }

    #[Test]
    public function cloneKeepsTheSegmentsBeforeAndAfterTheyAreSplit(): void
    {
        $fresh = new Name('\Some\Any', 3);
        $split = new Name('\Some\Any', 3);
        $split->toArray();

        foreach ([clone $fresh, clone $split] as $clone) {
            self::assertSame('\Some\Any', $clone->toString());
            self::assertSame(['Some', 'Any'], $clone->toArray());
            self::assertSame(3, $clone->offset);
        }
    }

    #[Test]
    public function serializationKeepsTheNameBeforeAndAfterTheSegmentsAreSplit(): void
    {
        $fresh = new Name('\Some\Any', 3);
        $split = new Name('\Some\Any', 3);
        $split->toArray();

        foreach ([$fresh, $split] as $name) {
            $restored = \unserialize(\serialize($name));

            self::assertInstanceOf(Name::class, $restored);
            self::assertSame('\Some\Any', $restored->toString());
            self::assertTrue($restored->isFullyQualified);
            self::assertSame(['Some', 'Any'], $restored->toArray());
            self::assertSame(3, $restored->offset);
        }
    }

    #[Test]
    public function qualificationDoesNotChangeTheOriginal(): void
    {
        $name = new Name('Foo');
        $name->toFullyQualified();

        self::assertSame('Foo', $name->unqualified);
        self::assertFalse($name->isFullyQualified);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function withAddedDataProvider(): iterable
    {
        yield 'simple names' => ['Some', 'Any', 'Some\Any'];
        yield 'qualified names' => ['Some\Any', 'Test\Class', 'Some\Any\Test\Class'];
        yield 'fully qualified added name' => ['Some', '\Any', 'Some\Any'];
    }

    /**
     * @param non-empty-string $base
     * @param non-empty-string $added
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('withAddedDataProvider')]
    public function withAddedAppendsSegmentsToTheEnd(string $base, string $added, string $expected): void
    {
        $name = (new Name($base, 3))->withAdded(new Name($added, 9));

        self::assertSame($expected, $name->unqualified);
        self::assertSame(3, $name->offset);
    }

    #[Test]
    public function withAddedKeepsTheLeadingSeparatorOfTheBase(): void
    {
        $name = (new Name('\Some'))->withAdded(new Name('Any'));

        self::assertSame('\Some\Any', $name->toString());
        self::assertTrue($name->isFullyQualified);

        $name = (new Name('\Some\Any'))->withAdded(new Name('\Test'));

        self::assertSame('\Some\Any\Test', $name->toString());
        self::assertTrue($name->isFullyQualified);
    }

    #[Test]
    public function withAddedKeepsTheNameRelativeWhenTheBaseIsRelative(): void
    {
        $name = (new Name('Some'))->withAdded(new Name('\Any'));

        self::assertSame('Some\Any', $name->toString());
        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function withAddedCanBeChained(): void
    {
        $name = (new Name('A'))
            ->withAdded(new Name('B'))
            ->withAdded(new Name('C\D'));

        self::assertSame('A\B\C\D', $name->unqualified);
        self::assertSame(['A', 'B', 'C', 'D'], $name->toArray());
    }

    #[Test]
    public function withAddedDoesNotChangeTheOriginals(): void
    {
        $base = new Name('Some');
        $added = new Name('Any');

        $base->withAdded($added);

        self::assertSame('Some', $base->unqualified);
        self::assertSame('Any', $added->unqualified);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function withReplacedDataProvider(): iterable
    {
        yield 'simple' => ['Some', 'Other', 'Other'];
        yield 'qualified' => ['Some\Any', 'Other', 'Some\Other'];
        yield 'long' => ['Some\Any\Test', 'Other', 'Some\Any\Other'];
        yield 'same segment' => ['Some\Any', 'Any', 'Some\Any'];
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $segment
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('withReplacedDataProvider')]
    public function withReplacedChangesTheLastSegment(string $value, string $segment, string $expected): void
    {
        $name = (new Name($value, 4))->withReplaced(new Name($segment, 9));

        self::assertSame($expected, $name->unqualified);
        self::assertSame($segment, $name->last());
    }

    #[Test]
    public function withReplacedKeepsTheOffsetOfTheName(): void
    {
        $name = (new Name('Some\Any', 4))->withReplaced(new Name('Other', 9));

        self::assertSame(4, $name->offset);
    }

    #[Test]
    public function withReplacedDoesNotChangeTheOriginals(): void
    {
        $name = new Name('Some\Any');
        $segment = new Name('Other');

        $name->withReplaced($segment);

        self::assertSame('Some\Any', $name->unqualified);
        self::assertSame('Other', $segment->unqualified);
    }

    #[Test]
    public function withReplacedKeepsTheLeadingSeparator(): void
    {
        $name = (new Name('\Some\Any'))->withReplaced(new Name('Other'));

        self::assertSame('\Some\Other', $name->toString());
        self::assertTrue($name->isFullyQualified);
    }

    #[Test]
    public function withReplacedKeepsTheLeadingSeparatorOfASimpleName(): void
    {
        $name = (new Name('\Some'))->withReplaced(new Name('Other'));

        self::assertSame('\Other', $name->toString());
        self::assertTrue($name->isFullyQualified);
    }

    #[Test]
    public function withReplacedIgnoresTheLeadingSeparatorOfTheArgument(): void
    {
        $relative = (new Name('Some'))->withReplaced(new Name('\Other'));
        $qualified = (new Name('Some\Any'))->withReplaced(new Name('\Other'));
        $absolute = (new Name('\Some'))->withReplaced(new Name('\Other'));

        self::assertSame('Other', $relative->toString());
        self::assertSame('Some\Other', $qualified->toString());
        self::assertSame('\Other', $absolute->toString());
    }

    #[Test]
    public function withAddedAndWithReplacedAcceptStrings(): void
    {
        $name = new Name('Some\Any');

        self::assertSame('Some\Any\Test', $name->withAdded('Test')->toString());
        self::assertSame('Some\Test', $name->withReplaced('Test')->toString());
    }

    #[Test]
    public function withAddedAndWithReplacedAcceptStringableObjects(): void
    {
        $name = new Name('Some\Any');
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Test';
            }
        };

        self::assertSame('Some\Any\Test', $name->withAdded($stringable)->toString());
        self::assertSame('Some\Test', $name->withReplaced($stringable)->toString());
    }

    #[Test]
    public function withAddedRejectsAnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Name('Some'))->withAdded('');
    }

    #[Test]
    public function withReplacedRejectsAnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Name('Some'))->withReplaced('\\');
    }

    #[Test]
    public function qualificationReturnsTheSameInstanceWhenThereIsNothingToChange(): void
    {
        $relative = new Name('Foo');
        $absolute = new Name('\Foo');

        self::assertSame($relative, $relative->toUnqualified());
        self::assertSame($absolute, $absolute->toFullyQualified());
        self::assertNotSame($relative, $relative->toFullyQualified());
        self::assertNotSame($absolute, $absolute->toUnqualified());
    }

    #[Test]
    public function simpleNameHasNoSeparatorsInside(): void
    {
        self::assertTrue((new Name('Foo'))->isSimple());
        self::assertTrue((new Name('\Foo'))->isSimple());
        self::assertFalse((new Name('Foo\Bar'))->isSimple());
        self::assertFalse((new Name('\Foo\Bar'))->isSimple());
    }

    #[Test]
    public function firstAndLastNeedNoSplitToWork(): void
    {
        $name = new Name('\Some\Any\Test');

        self::assertSame('Some', $name->first());
        self::assertSame('Test', $name->last());
        self::assertSame(['Some', 'Any', 'Test'], $name->toArray());
    }

    #[Test]
    public function splitSegmentsAreKeptForRepeatedCalls(): void
    {
        $name = new Name('Some\Any');

        self::assertSame($name->toArray(), $name->toArray());
        self::assertSame('Any', $name->last());
        self::assertSame('Some', $name->first());
    }
}
