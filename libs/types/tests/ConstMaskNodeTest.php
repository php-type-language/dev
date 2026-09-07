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
    public function namespaceIsNullByDefault(): void
    {
        $node = new ConstMaskNode(new MaskNode([new WildcardNode(), new Identifier('_SOME')]));

        self::assertNull($node->namespace);
        self::assertFalse($node->isFullyQualified);
    }

    #[Test]
    public function constructorStoresTheNamespace(): void
    {
        $namespace = Name::createFromString('Some\Any');
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            $namespace,
        );

        self::assertSame($namespace, $node->namespace);
    }

    /**
     * The leading separator belongs to the node, so a namespace written with
     * one is kept as a relative name and lifted into the flag.
     */
    #[Test]
    public function aFullyQualifiedNamespaceFillsTheFlag(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('\Some\Any'),
        );

        self::assertTrue($node->isFullyQualified);
        self::assertNotNull($node->namespace);
        self::assertFalse($node->namespace->isFullyQualified);
        self::assertSame('Some\Any', $node->namespace->toString());
    }

    #[Test]
    public function aRelativeNamespaceLeavesTheFlagUnset(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('Some\Any'),
        );

        self::assertFalse($node->isFullyQualified);
    }

    #[Test]
    public function constructorStoresTheFullyQualifiedFlag(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            true,
        );

        self::assertTrue($node->isFullyQualified);
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
