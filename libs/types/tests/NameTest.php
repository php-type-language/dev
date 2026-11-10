<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

final class NameTest extends TestCase
{
    /**
     * Cuts a name written as a string into the namespace and the last word.
     *
     * @param non-empty-string $text
     * @param int<0, max> $offset
     */
    private static function nameOf(string $text, int $offset = 0): Name
    {
        $separator = \strrpos($text, '\\');

        if ($separator === false) {
            return new Name($text, null, $offset);
        }

        /** @var non-empty-string $word */
        $word = \substr($text, $separator + 1);

        $kind = NamespaceKind::Qualified;
        $namespace = \substr($text, 0, $separator);

        if ($text[0] === '\\') {
            $kind = NamespaceKind::FullyQualified;
            $namespace = \substr($namespace, 1);
        }

        return new Name($word, new NamespaceName($namespace, $kind), $offset);
    }

    #[Test]
    public function valueNamespaceAndOffsetAreKept(): void
    {
        $namespace = new NamespaceName('Some');
        $name = new Name('Any', $namespace, 7);

        self::assertSame('Any', $name->value);
        self::assertSame($namespace, $name->namespace);
        self::assertSame('Some\Any', $name->toString());
        self::assertSame('Some\Any', (string) $name);
        self::assertSame(7, $name->offset);
    }

    #[Test]
    public function offsetAndNamespaceAreEmptyByDefault(): void
    {
        $name = new Name('Foo');

        self::assertSame(0, $name->offset);
        self::assertNull($name->namespace);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function invalidValueDataProvider(): iterable
    {
        yield 'separator' => ['\\'];
        yield 'leading separator' => ['\Foo'];
        yield 'trailing separator' => ['Foo\\'];
        yield 'separator inside' => ['Foo\Bar'];
    }

    /**
     * @param non-empty-string $value
     */
    #[Test]
    #[DataProvider('invalidValueDataProvider')]
    public function valueWithASeparatorIsRejected(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Name($value);
    }

    #[Test]
    public function emptyValueIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Name('');
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
        yield 'current namespace' => ['namespace\Foo', false, ['Foo']];
        yield 'current namespace and qualified' => ['namespace\Foo\Bar', false, ['Foo', 'Bar']];
    }

    /**
     * @param non-empty-string $text
     * @param non-empty-list<non-empty-string> $segments
     */
    #[Test]
    #[DataProvider('segmentsDataProvider')]
    public function segmentsAreMadeOutOfTheNamespaceAndTheValue(string $text, bool $isFullyQualified, array $segments): void
    {
        $name = self::nameOf($text);

        self::assertSame($isFullyQualified, $name->isFullyQualified());
        self::assertSame($segments, $name->toArray());
        self::assertSame($segments, \iterator_to_array($name));
        self::assertCount(\count($segments), $name);
        self::assertSame(\count($segments) === 1, $name->isSimple());
        self::assertSame($text, $name->toString());
    }

    #[Test]
    public function firstAndLastAreTheEdgesOfTheName(): void
    {
        $name = self::nameOf('\Some\Any\Test');

        self::assertSame('Some', $name->first());
        self::assertSame('Test', $name->last());
    }

    #[Test]
    public function firstAndLastOfASimpleNameAreTheSame(): void
    {
        $name = self::nameOf('Some');

        self::assertSame('Some', $name->first());
        self::assertSame('Some', $name->last());
    }

    #[Test]
    public function firstOfANameInTheRootNamespaceIsItsValue(): void
    {
        $name = new Name('Some', new NamespaceName('', NamespaceKind::FullyQualified));

        self::assertSame('Some', $name->first());
        self::assertSame(['Some'], $name->toArray());
        self::assertSame('\Some', $name->toString());
    }

    #[Test]
    public function keywordOfTheCurrentNamespaceIsNotAWord(): void
    {
        $name = new Name('Some', new NamespaceName('namespace'));

        self::assertSame(NamespaceKind::NamespaceRelative, $name->namespace?->kind);
        self::assertSame('Some', $name->first());
        self::assertSame(['Some'], $name->toArray());
        self::assertSame('namespace\Some', $name->toString());
        self::assertFalse($name->isFullyQualified());
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
        yield 'fully qualified' => ['\self', false];
        yield 'current namespace' => ['namespace\self', false];
    }

    /**
     * @param non-empty-string $text
     */
    #[Test]
    #[DataProvider('specialDataProvider')]
    public function specialNameIsRecognized(string $text, bool $expected): void
    {
        self::assertSame($expected, self::nameOf($text)->isSpecial());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function unqualifiedDataProvider(): iterable
    {
        yield 'a simple name' => ['Any', 'Any'];
        yield 'a qualified name' => ['Some\Any', 'Any'];
        yield 'a fully qualified name' => ['\Some\Any', 'Any'];
        yield 'a root name' => ['\Any', 'Any'];
        yield 'a name of the current namespace' => ['namespace\Any', 'Any'];
    }

    /**
     * @param non-empty-string $text
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('unqualifiedDataProvider')]
    public function unqualifiedNameIsTheLastWordAlone(string $text, string $expected): void
    {
        $name = self::nameOf($text, 5)->toUnqualified();

        self::assertSame($expected, $name->toString());
        self::assertNull($name->namespace);
        self::assertSame(5, $name->offset);
    }

    #[Test]
    public function unqualifiedNameOfASimpleNameIsTheSameNode(): void
    {
        $name = new Name('Any');

        self::assertSame($name, $name->toUnqualified());
    }

    #[Test]
    public function virtualNameHasAHyphen(): void
    {
        self::assertTrue(self::nameOf('non-empty-string')->isVirtual());
        self::assertFalse(self::nameOf('Some\Any')->isVirtual());
    }

    #[Test]
    public function hyphenInAnySegmentMakesTheNameVirtual(): void
    {
        self::assertTrue(self::nameOf('Some\non-empty')->isVirtual());
        self::assertTrue(self::nameOf('\non-empty\Any')->isVirtual());
    }

    #[Test]
    public function simpleNameHasNoNamespaceWords(): void
    {
        self::assertTrue(self::nameOf('Foo')->isSimple());
        self::assertTrue(self::nameOf('\Foo')->isSimple());
        self::assertTrue(self::nameOf('namespace\Foo')->isSimple());
        self::assertFalse(self::nameOf('Foo\Bar')->isSimple());
        self::assertFalse(self::nameOf('\Foo\Bar')->isSimple());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function lowerStringDataProvider(): iterable
    {
        yield 'simple' => ['FOO', 'foo'];
        yield 'qualified' => ['Some\ANY', 'some\any'];
        yield 'fully qualified' => ['\Some\Any', '\some\any'];
        yield 'current namespace' => ['namespace\Some\Any', 'namespace\some\any'];
    }

    /**
     * @param non-empty-string $text
     * @param non-empty-string $expected
     */
    #[Test]
    #[DataProvider('lowerStringDataProvider')]
    public function toLowerStringLowersTheWholeName(string $text, string $expected): void
    {
        self::assertSame($expected, self::nameOf($text)->toLowerString());
    }

    #[Test]
    public function nameIsStringableCountableAndIterable(): void
    {
        $name = self::nameOf('Some\Any');

        self::assertInstanceOf(\Stringable::class, $name);
        self::assertInstanceOf(\Countable::class, $name);
        self::assertInstanceOf(\IteratorAggregate::class, $name);
    }

    #[Test]
    public function cloneKeepsTheSegmentsBeforeAndAfterTheyAreSplit(): void
    {
        $fresh = self::nameOf('\Some\Any', 3);
        $split = self::nameOf('\Some\Any', 3);
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
        $fresh = self::nameOf('\Some\Any', 3);
        $split = self::nameOf('\Some\Any', 3);
        $split->toArray();

        foreach ([$fresh, $split] as $name) {
            $restored = \unserialize(\serialize($name));

            self::assertInstanceOf(Name::class, $restored);
            self::assertSame('\Some\Any', $restored->toString());
            self::assertTrue($restored->isFullyQualified());
            self::assertSame(['Some', 'Any'], $restored->toArray());
            self::assertSame(3, $restored->offset);
        }
    }

    #[Test]
    public function qualifiedNamespaceOfNoWordsAddsNothingToTheName(): void
    {
        $name = new Name('Any', new NamespaceName());

        self::assertSame('Any', $name->toString());
        self::assertFalse($name->isFullyQualified());
    }

    #[Test]
    public function splitSegmentsAreKeptForRepeatedCalls(): void
    {
        $name = self::nameOf('Some\Any');

        self::assertSame($name->toArray(), $name->toArray());
        self::assertSame('Any', $name->last());
        self::assertSame('Some', $name->first());
    }
}
