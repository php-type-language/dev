<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

final class NamespaceNameTest extends TestCase
{
    #[Test]
    public function valueKindAndOffsetAreKept(): void
    {
        $namespace = new NamespaceName('Some\Any', NamespaceKind::FullyQualified, 4);

        self::assertSame('Some\Any', $namespace->value);
        self::assertSame(NamespaceKind::FullyQualified, $namespace->kind);
        self::assertSame(4, $namespace->offset);
    }

    #[Test]
    public function namespaceIsQualifiedAndEmptyByDefault(): void
    {
        $namespace = new NamespaceName();

        self::assertSame('', $namespace->value);
        self::assertSame(NamespaceKind::Qualified, $namespace->kind);
        self::assertSame(0, $namespace->offset);
    }

    /**
     * @return iterable<non-empty-string, array{string, NamespaceKind, string, list<non-empty-string>}>
     */
    public static function writtenDataProvider(): iterable
    {
        yield 'qualified' => ['Some', NamespaceKind::Qualified, 'Some', ['Some']];
        yield 'qualified and deeper' => ['Some\Any', NamespaceKind::Qualified, 'Some\Any', ['Some', 'Any']];
        yield 'fully qualified' => ['Some', NamespaceKind::FullyQualified, '\Some', ['Some']];
        yield 'fully qualified and deeper' => ['Some\Any', NamespaceKind::FullyQualified, '\Some\Any', ['Some', 'Any']];
        yield 'root' => ['', NamespaceKind::FullyQualified, '\\', []];
        yield 'current' => ['', NamespaceKind::NamespaceRelative, 'namespace', []];
        yield 'relative to the current' => ['Some', NamespaceKind::NamespaceRelative, 'namespace\Some', ['Some']];
        yield 'empty' => ['', NamespaceKind::Qualified, '', []];
    }

    /**
     * @param list<non-empty-string> $words
     */
    #[Test]
    #[DataProvider('writtenDataProvider')]
    public function namespaceIsWrittenTheWayItIsRead(string $value, NamespaceKind $kind, string $written, array $words): void
    {
        $namespace = new NamespaceName($value, $kind);

        self::assertSame($written, $namespace->toString());
        self::assertSame($written, (string) $namespace);
        self::assertSame($words, $namespace->toArray());
    }

    #[Test]
    public function onlyTheLeadingSeparatorWithNoWordsIsTheRoot(): void
    {
        self::assertTrue((new NamespaceName('', NamespaceKind::FullyQualified))->isRoot());
        self::assertFalse((new NamespaceName('Some', NamespaceKind::FullyQualified))->isRoot());
        self::assertFalse((new NamespaceName('', NamespaceKind::NamespaceRelative))->isRoot());
        self::assertFalse((new NamespaceName(''))->isRoot());
    }

    /**
     * @return iterable<non-empty-string, array{string, string}>
     */
    public static function keywordDataProvider(): iterable
    {
        yield 'alone' => ['namespace', ''];
        yield 'with a word' => ['namespace\Some', 'Some'];
        yield 'with words' => ['namespace\Some\Any', 'Some\Any'];
        yield 'in upper case' => ['NAMESPACE\Some', 'Some'];
        yield 'in mixed case' => ['Namespace', ''];
    }

    #[Test]
    #[DataProvider('keywordDataProvider')]
    public function keywordTurnsQualifiedNamespaceIntoRelativeToTheCurrentOne(string $value, string $words): void
    {
        $namespace = new NamespaceName($value);

        self::assertSame(NamespaceKind::NamespaceRelative, $namespace->kind);
        self::assertSame($words, $namespace->value);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function notKeywordDataProvider(): iterable
    {
        yield 'a word behind' => ['Some\namespace'];
        yield 'a longer word' => ['namespaces'];
        yield 'a longer word with a word' => ['namespaces\Some'];
        yield 'inside' => ['Some\namespace\Any'];
    }

    #[Test]
    #[DataProvider('notKeywordDataProvider')]
    public function keywordMustBeTheFirstWholeWord(string $value): void
    {
        $namespace = new NamespaceName($value);

        self::assertSame(NamespaceKind::Qualified, $namespace->kind);
        self::assertSame($value, $namespace->value);
    }

    #[Test]
    public function keywordOfAFullyQualifiedNamespaceIsAWord(): void
    {
        $namespace = new NamespaceName('namespace\Some', NamespaceKind::FullyQualified);

        self::assertSame(NamespaceKind::FullyQualified, $namespace->kind);
        self::assertSame('namespace\Some', $namespace->value);
    }
}
