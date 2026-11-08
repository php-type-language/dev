<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Shape\FieldNodePrinter;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\NamedFieldNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Name;
use TypeLang\Type\Shape\NamedFieldNode;

#[CoversClass(NamedFieldNodePrinter::class)]
final class NamedFieldNodePrinterTest extends NodePrinterTestCase
{
    public function testKeyAndTypeAreJoinedByTheColon(): void
    {
        self::assertSame('name: int', self::pretty(new NamedFieldNode(new Name('name'), self::named('int'))));
    }

    public function testOptionalMarkFollowsTheKey(): void
    {
        self::assertSame('name?: int', self::pretty(new NamedFieldNode(new Name('name'), self::named('int'), true)));
    }

    public function testUnionOfTheTypeNeedsNoBrackets(): void
    {
        $field = new NamedFieldNode(new Name('name'), self::union(self::named('int'), self::named('string')));

        self::assertSame('name: int|string', self::pretty($field));
    }
}
