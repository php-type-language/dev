<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name\NamespaceName;
use TypeLang\Type\Tests\TestCase;

final class ConstMaskTypeNodeTest extends TestCase
{
    private static function mask(): MaskNode
    {
        return new MaskNode([new Identifier('SOME_'), new WildcardMaskNode()]);
    }

    #[Test]
    public function theNamespaceIsEmptyByDefault(): void
    {
        $node = new ConstMaskTypeNode(self::mask());

        self::assertCount(0, $node->namespace);
        self::assertFalse($node->namespace->isFullyQualified);
    }

    #[Test]
    public function theNamespaceSaysTheReferenceIsFullyQualified(): void
    {
        $node = new ConstMaskTypeNode(self::mask(), NamespaceName::from('\Some\Any'));

        self::assertTrue($node->namespace->isFullyQualified);
        self::assertSame('\Some\Any', $node->namespace->toString());
    }

    #[Test]
    public function aMaskWithNoNamespaceMayBeFullyQualified(): void
    {
        $node = new ConstMaskTypeNode(self::mask(), new NamespaceName([], true));

        self::assertCount(0, $node->namespace);
        self::assertTrue($node->namespace->isFullyQualified);
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
