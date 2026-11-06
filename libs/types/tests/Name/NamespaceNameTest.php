<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Name;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name\NamespaceName;
use TypeLang\Type\Tests\TestCase;

final class NamespaceNameTest extends TestCase
{
    #[Test]
    public function mayBeEmpty(): void
    {
        $namespace = new NamespaceName([]);

        self::assertCount(0, $namespace);
        self::assertFalse($namespace->isFullyQualified);
        self::assertSame('', $namespace->toString());
    }

    #[Test]
    public function emptyFullyQualifiedNamespaceIsTheRoot(): void
    {
        $namespace = new NamespaceName([], true);

        self::assertSame('\\', $namespace->toString());
    }

    #[Test]
    public function fromAcceptsEmptyString(): void
    {
        self::assertCount(0, NamespaceName::from(''));
    }

    #[Test]
    public function fromDetectsTheRoot(): void
    {
        $namespace = NamespaceName::from('\\');

        self::assertCount(0, $namespace);
        self::assertTrue($namespace->isFullyQualified);
    }

    #[Test]
    public function keepsTheOperationsOfAName(): void
    {
        $namespace = NamespaceName::from('Some\Any');

        self::assertSame('\Some\Any', $namespace->toFullyQualified()->toString());
        self::assertSame(['Some', 'Any'], $namespace->toArrayStrings());
        self::assertSame('Some', $namespace->slice(0, 1)->toString());
    }

    #[Test]
    public function isNotASimpleNameWhenEmpty(): void
    {
        self::assertFalse((new NamespaceName([]))->isSimple());
        self::assertFalse((new NamespaceName([]))->isSpecial());
        self::assertFalse((new NamespaceName([]))->isBuiltin());
    }

    #[Test]
    public function fromReturnsAnExistingNamespaceWithTheSameOffset(): void
    {
        $namespace = NamespaceName::from('Some\Any', 4);

        self::assertSame($namespace, NamespaceName::from($namespace, 4));
    }

    #[Test]
    public function fromCopiesAnExistingNamespaceWithAnotherOffset(): void
    {
        $namespace = NamespaceName::from('\Some\Any', 4);
        $copy = NamespaceName::from($namespace, 9);

        self::assertNotSame($namespace, $copy);
        self::assertSame('\Some\Any', $copy->toString());
        self::assertSame(9, $copy->offset);
    }
}
