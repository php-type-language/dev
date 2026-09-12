<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\MaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\WildcardNode;

final class ConstMaskNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresTheMask(): void
    {
        $mask = new MaskNode([new Identifier('SOME_CONST'), new WildcardNode()]);
        $node = new ConstMaskNode($mask);

        self::assertSame($mask, $node->mask);
    }

    #[Test]
    public function aMaskIsWrittenWithNoNamespaceByDefault(): void
    {
        $node = new ConstMaskNode(new MaskNode([new WildcardNode(), new Identifier('_SOME')]));

        self::assertFalse($node->namespaceOrFullyQualified);
        self::assertFalse($node->isFullyQualified());
    }

    #[Test]
    public function constructorStoresTheNamespace(): void
    {
        $namespace = Name::createFromString('Some\Any');
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            $namespace,
        );

        self::assertSame($namespace, $node->namespaceOrFullyQualified);
        self::assertFalse($node->isFullyQualified());
    }

    /**
     * The leading separator belongs to the namespace, so the namespace is the
     * one that says whether the reference is a fully qualified one.
     */
    #[Test]
    public function theNamespaceSaysTheReferenceIsFullyQualified(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('\Some\Any'),
        );

        self::assertTrue($node->isFullyQualified());
    }

    /**
     * A mask written with no namespace has nothing to carry that separator,
     * so it is passed on its own.
     */
    #[Test]
    public function aMaskWithNoNamespaceIsFullyQualifiedOnItsOwn(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            true,
        );

        self::assertTrue($node->namespaceOrFullyQualified);
        self::assertTrue($node->isFullyQualified());
    }

    #[Test]
    public function theSeparatorIsSetOnTheNamespaceItIsWrittenWith(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('Some\Any'),
        );

        $node->setFullyQualified();

        self::assertTrue($node->isFullyQualified());
        self::assertInstanceOf(Name::class, $node->namespaceOrFullyQualified);
        self::assertSame('\Some\Any', $node->namespaceOrFullyQualified->toString());
    }

    #[Test]
    public function theSeparatorIsTakenOffTheNamespaceItIsWrittenWith(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('\Some\Any'),
        );

        $node->setFullyQualified(false);

        self::assertFalse($node->isFullyQualified());
        self::assertInstanceOf(Name::class, $node->namespaceOrFullyQualified);
        self::assertSame('Some\Any', $node->namespaceOrFullyQualified->toString());
    }

    #[Test]
    public function theSeparatorIsSetOnAMaskWrittenWithNoNamespace(): void
    {
        $node = new ConstMaskNode(new MaskNode([new Identifier('SOME_'), new WildcardNode()]));

        $node->setFullyQualified();

        self::assertTrue($node->namespaceOrFullyQualified);
        self::assertTrue($node->isFullyQualified());
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new ConstMaskNode(new MaskNode([new Identifier('FOO'), new WildcardNode()]));

        self::assertSame(0, $node->offset);
    }

    #[Test]
    public function aMaskIsWrittenTheWayItIsRead(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([
                new Identifier('SOME'),
                new WildcardNode(),
                new Identifier('ANY'),
                new WildcardNode(),
            ]),
        );

        self::assertSame('SOME*ANY*', $node->mask->toString());
        self::assertSame(['SOME', 'ANY'], $node->mask->getSegmentsAsStrings());
    }
}
