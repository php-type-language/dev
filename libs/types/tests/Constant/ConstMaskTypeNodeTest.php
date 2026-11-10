<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Tests\TestCase;

final class ConstMaskTypeNodeTest extends TestCase
{
    private static function mask(): MaskNode
    {
        return new MaskNode([new Identifier('SOME_'), new WildcardMaskNode()]);
    }

    #[Test]
    public function maskMayBeWrittenWithNoOwner(): void
    {
        $node = new ConstMaskTypeNode(null, self::mask());

        self::assertNull($node->owner);
    }

    #[Test]
    public function theNamespaceIsKeptAsItIsWritten(): void
    {
        $namespace = new NamespaceName('Some\Any', NamespaceKind::FullyQualified);
        $node = new ConstMaskTypeNode($namespace, self::mask());

        self::assertSame($namespace, $node->owner);
        self::assertSame('\Some\Any', $node->owner->toString());
    }

    #[Test]
    public function theRootNamespaceHasNoWords(): void
    {
        $node = new ConstMaskTypeNode(new NamespaceName('', NamespaceKind::FullyQualified), self::mask());

        self::assertTrue($node->owner instanceof NamespaceName && $node->owner->isRoot());
        self::assertTrue($node->isFullyQualified());
    }

    #[Test]
    public function aMaskIsNotFullyQualifiedByDefault(): void
    {
        self::assertFalse((new ConstMaskTypeNode(null, self::mask()))->isFullyQualified());
    }

    #[Test]
    public function aNamespaceTellsWhetherTheMaskIsFullyQualified(): void
    {
        $relative = new ConstMaskTypeNode(new NamespaceName('Some\Any'), self::mask());
        $current = new ConstMaskTypeNode(new NamespaceName('namespace\Some\Any'), self::mask());
        $absolute = new ConstMaskTypeNode(new NamespaceName('Some\Any', NamespaceKind::FullyQualified), self::mask());

        self::assertFalse($relative->isFullyQualified());
        self::assertFalse($current->isFullyQualified());
        self::assertTrue($absolute->isFullyQualified());
    }

    #[Test]
    public function aMaskIsWrittenTheWayItIsRead(): void
    {
        $node = new ConstMaskTypeNode(
            null,
            new MaskNode([
                new Identifier('SOME'),
                new WildcardMaskNode(),
                new Identifier('ANY'),
                new WildcardMaskNode(),
            ]),
        );

        self::assertSame('SOME*ANY*', $node->mask->toString());
        self::assertSame(['SOME', 'ANY'], $node->mask->getSegmentsAsStrings());
    }

    #[Test]
    public function maskMayBelongToAClass(): void
    {
        $class = new Name('Any', new NamespaceName('Some', NamespaceKind::FullyQualified));
        $node = new ConstMaskTypeNode($class, new MaskNode([new WildcardMaskNode()]));

        self::assertSame($class, $node->owner);
        self::assertSame([], $node->mask->getSegments());
        self::assertTrue($node->isFullyQualified());
    }
}
