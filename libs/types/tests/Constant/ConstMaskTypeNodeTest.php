<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\Tests\TestCase;

final class ConstMaskTypeNodeTest extends TestCase
{
    private static function mask(): MaskNode
    {
        return new MaskNode([new Name('SOME_'), new WildcardMaskNode()]);
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
        $node = new ConstMaskTypeNode(self::mask(), new Name('\Some\Any'));

        self::assertSame('\Some\Any', $node->namespace?->toString());
    }

    #[Test]
    public function theRootNamespaceIsALoneSeparator(): void
    {
        self::markTestIncomplete('A Name cannot be a lone separator, the root namespace has no representation yet');
    }

    #[Test]
    public function aMaskIsWrittenTheWayItIsRead(): void
    {
        $node = new ConstMaskTypeNode(
            new MaskNode([
                new Name('SOME'),
                new WildcardMaskNode(),
                new Name('ANY'),
                new WildcardMaskNode(),
            ]),
        );

        self::assertSame('SOME*ANY*', $node->mask->toString());
        self::assertSame(['SOME', 'ANY'], $node->mask->getSegmentsAsStrings());
    }
}
