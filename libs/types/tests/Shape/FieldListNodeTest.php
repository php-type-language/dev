<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Shape\FieldListNode;
use TypeLang\Type\Tests\TestCase;

final class FieldListNodeTest extends TestCase
{

    #[Test]
    public function sealedByDefault(): void
    {
        $list = new FieldListNode();

        self::assertTrue($list->isSealed);
    }

    #[Test]
    public function constructorAcceptsUnsealedFlag(): void
    {
        $list = new FieldListNode([], false);

        self::assertFalse($list->isSealed);
    }
}
