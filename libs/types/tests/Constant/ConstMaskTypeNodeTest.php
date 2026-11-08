<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
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
    public function theNamespaceIsNullByDefault(): void
    {
        $node = new ConstMaskTypeNode(self::mask());

        self::assertNull($node->namespace);
    }

    #[Test]
    public function theNamespaceIsKeptAsItIsWritten(): void
    {
        $namespace = new NamespaceName('Some\Any', NamespaceKind::FullyQualified);
        $node = new ConstMaskTypeNode(self::mask(), $namespace);

        self::assertSame($namespace, $node->namespace);
        self::assertSame('\Some\Any', $node->namespace->toString());
    }

    #[Test]
    public function theRootNamespaceHasNoWords(): void
    {
        $node = new ConstMaskTypeNode(self::mask(), new NamespaceName('', NamespaceKind::FullyQualified));

        self::assertTrue($node->namespace?->isRoot());
        self::assertTrue($node->isFullyQualified());
    }

    #[Test]
    public function aMaskIsNotFullyQualifiedByDefault(): void
    {
        self::assertFalse((new ConstMaskTypeNode(self::mask()))->isFullyQualified());
    }

    #[Test]
    public function aNamespaceTellsWhetherTheMaskIsFullyQualified(): void
    {
        $relative = new ConstMaskTypeNode(self::mask(), new NamespaceName('Some\Any'));
        $current = new ConstMaskTypeNode(self::mask(), new NamespaceName('namespace\Some\Any'));
        $absolute = new ConstMaskTypeNode(self::mask(), new NamespaceName('Some\Any', NamespaceKind::FullyQualified));

        self::assertFalse($relative->isFullyQualified());
        self::assertFalse($current->isFullyQualified());
        self::assertTrue($absolute->isFullyQualified());
    }

    #[Test]
    public function aMaskIsWrittenTheWayItIsRead(): void
    {
        $node = new ConstMaskTypeNode(
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
}
