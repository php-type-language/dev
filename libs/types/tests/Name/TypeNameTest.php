<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Name;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name\NamespaceName;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\Tests\TestCase;

final class TypeNameTest extends TestCase
{
    private function id(string $v): Identifier
    {
        return new Identifier($v);
    }

    #[Test]
    public function isSimpleIsTrueForSingleSegment(): void
    {
        $name = new TypeName([$this->id('Foo')]);

        self::assertTrue($name->isSimple());
    }

    #[Test]
    public function isSimpleIsFalseForMultipleSegments(): void
    {
        $name = new TypeName([$this->id('Foo'), $this->id('Bar')]);

        self::assertFalse($name->isSimple());
    }

    #[Test]
    public function isSpecialIsTrueForSpecialSingleSegment(): void
    {
        $name = new TypeName([$this->id('self')]);

        self::assertTrue($name->isSpecial());
    }

    #[Test]
    public function isSpecialIsFalseForMultiSegmentNameStartingWithSpecial(): void
    {
        $name = new TypeName([$this->id('self'), $this->id('Foo')]);

        self::assertFalse($name->isSpecial());
    }

    #[Test]
    public function isBuiltinIsTrueForBuiltinSingleSegment(): void
    {
        $name = new TypeName([$this->id('int')]);

        self::assertTrue($name->isBuiltin());
    }

    #[Test]
    public function isBuiltinIsFalseForMultiSegmentName(): void
    {
        $name = new TypeName([$this->id('int'), $this->id('Foo')]);

        self::assertFalse($name->isBuiltin());
    }

    #[Test]
    public function fromReturnsAnExistingNameOfTheSameKind(): void
    {
        $name = TypeName::from('Foo');

        self::assertSame($name, TypeName::from($name));
    }

    #[Test]
    public function fromCopiesAnExistingNameWithAnotherOffset(): void
    {
        $name = TypeName::from('\Foo\Bar', 3);
        $copy = TypeName::from($name, 9);

        self::assertNotSame($name, $copy);
        self::assertSame(9, $copy->offset);
        self::assertSame('\Foo\Bar', $copy->toString());
        self::assertSame(3, $name->offset);
    }

    #[Test]
    public function fromParsesSimpleName(): void
    {
        $name = TypeName::from('Foo');

        self::assertSame('Foo', $name->toString());
        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function fromParsesQualifiedName(): void
    {
        $name = TypeName::from('Foo\Bar\Baz');

        self::assertSame('Foo\Bar\Baz', $name->toString());
        self::assertCount(3, $name);
    }

    #[Test]
    public function fromDetectsFullyQualified(): void
    {
        $name = TypeName::from('\Foo\Bar');

        self::assertTrue($name->isFullyQualified);
        self::assertSame('\Foo\Bar', $name->toString());
    }

    #[Test]
    public function sliceReturnsSubName(): void
    {
        $name = TypeName::from('A\B\C');
        $sliced = $name->slice(1);

        self::assertSame('B\C', $sliced->toString());
    }

    #[Test]
    public function sliceWithLength(): void
    {
        $name = TypeName::from('A\B\C');
        $sliced = $name->slice(0, 2);

        self::assertSame('A\B', $sliced->toString());
    }

    #[Test]
    public function withAddedAppendsSegments(): void
    {
        $a = TypeName::from('Some\Any');
        $b = TypeName::from('Test\Class');

        $result = $a->withAdded($b);

        self::assertSame('Some\Any\Test\Class', $result->toString());
    }

    #[Test]
    public function expandAliasDropsFirstSegmentOfAdded(): void
    {
        $name = TypeName::from('Some\Any');
        $alias = TypeName::from('Any\Class');

        $result = $name->expandAlias($alias);

        self::assertSame('Some\Any\Class', $result->toString());
    }

    #[Test]
    public function toFullyQualifiedConvertsName(): void
    {
        $name = TypeName::from('Foo\Bar');
        $fq = $name->toFullyQualified();

        self::assertTrue($fq->isFullyQualified);
        self::assertSame('\Foo\Bar', $fq->toString());
    }

    #[Test]
    public function toFullyQualifiedReturnsCloneIfAlreadyFullyQualified(): void
    {
        $name = TypeName::from('\Foo\Bar');
        $fq = $name->toFullyQualified();

        self::assertTrue($fq->isFullyQualified);
    }

    #[Test]
    public function toUnqualifiedConvertsName(): void
    {
        $name = TypeName::from('\Foo\Bar');
        $uq = $name->toUnqualified();

        self::assertFalse($uq->isFullyQualified);
        self::assertSame('Foo\Bar', $uq->toString());
    }

    #[Test]
    public function toStringArrayReturnsSegmentStrings(): void
    {
        $name = TypeName::from('A\B\C');

        self::assertSame(['A', 'B', 'C'], $name->toArrayStrings());
    }

    #[Test]
    public function toLowerStringArrayReturnsLowercasedSegments(): void
    {
        $name = TypeName::from('Foo\Bar');

        self::assertSame(['foo', 'bar'], $name->toArrayLowercaseStrings());
    }

    #[Test]
    public function toUnqualifiedStringDoesNotIncludeLeadingBackslash(): void
    {
        $name = TypeName::from('\Foo\Bar');

        self::assertSame('Foo\Bar', $name->toUnqualifiedString());
    }

    #[Test]
    public function toFullyQualifiedStringIncludesLeadingBackslash(): void
    {
        $name = TypeName::from('Foo\Bar');

        self::assertSame('\Foo\Bar', $name->toFullyQualifiedString());
    }

    #[Test]
    public function toLowerStringReturnsLowercasedName(): void
    {
        $name = TypeName::from('Foo\Bar');

        self::assertSame('foo\bar', $name->toLowerString());
    }

    #[Test]
    public function constructorThrowsOnEmptySegmentsArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TypeName([]);
    }

    #[Test]
    public function toUnqualifiedLowerStringLowercasesNameWithoutLeadingDelimiter(): void
    {
        $name = TypeName::from('\Vendor\Package\SomeClass');

        self::assertSame('vendor\package\someclass', $name->toUnqualifiedLowerString());
    }

    #[Test]
    public function toFullyQualifiedLowerStringKeepsLeadingDelimiter(): void
    {
        $name = TypeName::from('\Vendor\Package\SomeClass');

        self::assertSame('\vendor\package\someclass', $name->toFullyQualifiedLowerString());
    }

    #[Test]
    public function toFullyQualifiedStringIsIndependentOfTheQualificationFlag(): void
    {
        $name = TypeName::from('Vendor\SomeClass');

        self::assertFalse($name->isFullyQualified);
        self::assertSame('\Vendor\SomeClass', $name->toFullyQualifiedString());
        self::assertSame('\vendor\someclass', $name->toFullyQualifiedLowerString());
    }

    #[Test]
    public function toStringDependsOnTheQualificationFlag(): void
    {
        $unqualified = new TypeName([$this->id('Foo')], false);
        $qualified = new TypeName([$this->id('Foo')], true);

        self::assertSame('Foo', $unqualified->toString());
        self::assertSame('\Foo', $qualified->toString());
    }

    #[Test]
    public function fromIgnoresRepeatedDelimiters(): void
    {
        $name = TypeName::from('Foo\\\\Bar');

        self::assertSame(['Foo', 'Bar'], $name->toArrayStrings());
    }

    #[Test]
    public function fromIgnoresTrailingDelimiter(): void
    {
        $name = TypeName::from('Foo\Bar\\');

        self::assertSame(['Foo', 'Bar'], $name->toArrayStrings());
        self::assertFalse($name->isFullyQualified);
    }

    #[Test]
    public function fromAcceptsStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'Foo\Bar';
            }
        };

        self::assertSame(['Foo', 'Bar'], TypeName::from($stringable)->toArrayStrings());
    }

    #[Test]
    public function fromThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TypeName::from('');
    }

    #[Test]
    public function fromThrowsOnDelimiterOnlyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TypeName::from('\\');
    }

    #[Test]
    public function sliceKeepsQualificationFlag(): void
    {
        $name = TypeName::from('\Foo\Bar\Baz');

        $sliced = $name->slice(1);

        self::assertSame(['Bar', 'Baz'], $sliced->toArrayStrings());
        self::assertTrue($sliced->isFullyQualified);
    }

    #[Test]
    public function sliceDoesNotModifyTheOriginalName(): void
    {
        $name = TypeName::from('Foo\Bar\Baz');

        $name->slice(1);

        self::assertSame(['Foo', 'Bar', 'Baz'], $name->toArrayStrings());
    }

    #[Test]
    public function withAddedKeepsQualificationFlagOfTheReceiver(): void
    {
        $name = TypeName::from('\Some\Any');

        $result = $name->withAdded(TypeName::from('Test\Class'));

        self::assertSame(['Some', 'Any', 'Test', 'Class'], $result->toArrayStrings());
        self::assertTrue($result->isFullyQualified);
    }

    #[Test]
    public function withAddedDoesNotModifyArguments(): void
    {
        $name = TypeName::from('Some\Any');
        $added = TypeName::from('Test');

        $name->withAdded($added);

        self::assertSame(['Some', 'Any'], $name->toArrayStrings());
        self::assertSame(['Test'], $added->toArrayStrings());
    }

    #[Test]
    public function expandAliasReplacesTheAliasSegment(): void
    {
        $name = TypeName::from('TypeLang\Parser\Exception');

        $result = $name->expandAlias(TypeName::from('Error\SemanticException'));

        self::assertSame('TypeLang\Parser\Exception\SemanticException', $result->toString());
    }

    #[Test]
    public function expandAliasSimpleNameReturnsTheReceiverSegments(): void
    {
        $name = TypeName::from('TypeLang\Parser\Node');

        $result = $name->expandAlias(TypeName::from('Node'));

        self::assertSame('TypeLang\Parser\Node', $result->toString());
    }

    #[Test]
    public function toFullyQualifiedKeepsSegments(): void
    {
        $name = TypeName::from('Foo\Bar');

        $result = $name->toFullyQualified();

        self::assertTrue($result->isFullyQualified);
        self::assertSame(['Foo', 'Bar'], $result->toArrayStrings());
        self::assertFalse($name->isFullyQualified, 'The original name must not be modified');
    }

    #[Test]
    public function toUnqualifiedKeepsSegments(): void
    {
        $name = TypeName::from('\Foo\Bar');

        $result = $name->toUnqualified();

        self::assertFalse($result->isFullyQualified);
        self::assertSame(['Foo', 'Bar'], $result->toArrayStrings());
        self::assertTrue($name->isFullyQualified, 'The original name must not be modified');
    }

    #[Test]
    public function toUnqualifiedOfUnqualifiedNameReturnsEqualName(): void
    {
        $name = TypeName::from('Foo\Bar');

        $result = $name->toUnqualified();

        self::assertFalse($result->isFullyQualified);
        self::assertSame('Foo\Bar', $result->toString());
    }

    #[Test]
    public function serializationRoundtripPreservesQualificationFlag(): void
    {
        $name = TypeName::from('\Foo\Bar');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertTrue($restored->isFullyQualified);
        self::assertSame('\Foo\Bar', $restored->toString());
    }

    #[Test]
    public function serializationRoundtripRestoresBoundaryParts(): void
    {
        $name = TypeName::from('Foo\Bar\Baz');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertSame('Foo', $restored->first->value);
        self::assertSame('Baz', $restored->last->value);
        self::assertSame('Foo', $restored->first->value);
        self::assertSame('Baz', $restored->last->value);
    }

    #[Test]
    public function unserializedNameIsFullyUsable(): void
    {
        $name = TypeName::from('self');

        /** @var Name $restored */
        $restored = \unserialize(\serialize($name));

        self::assertTrue($restored->isSimple());
        self::assertTrue($restored->isSpecial());
        self::assertFalse($restored->isBuiltin());
        self::assertSame('self', $restored->first->value);
    }

    #[Test]
    public function constructorThrowsOnNonIdentifierParts(): void
    {
        $this->expectException(\TypeError::class);

        /** @phpstan-ignore-next-line */
        new TypeName(['Foo']);
    }

    #[Test]
    public function sliceBeyondTheEndGivesAnEmptyNamespace(): void
    {
        $sliced = TypeName::from('Foo\Bar')->slice(5);

        self::assertInstanceOf(NamespaceName::class, $sliced);
        self::assertCount(0, $sliced);
    }

    #[Test]
    public function sliceOfZeroLengthGivesAnEmptyNamespace(): void
    {
        $sliced = TypeName::from('\Foo\Bar')->slice(0, 0);

        self::assertCount(0, $sliced);
        self::assertTrue($sliced->isFullyQualified);
    }

    #[Test]
    public function sliceKeepsTheOffset(): void
    {
        self::assertSame(7, TypeName::from('Foo\Bar', 7)->slice(1)->offset);
    }

    #[Test]
    public function offsetIsKeptByTheOperationsOnAName(): void
    {
        $name = TypeName::from('Foo\Bar', 7);

        self::assertSame(7, $name->toFullyQualified()->offset);
        self::assertSame(7, $name->withAdded(TypeName::from('Baz'))->offset);
        self::assertSame(7, $name->expandAlias(TypeName::from('Alias\Baz'))->offset);
    }
}
