<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;

final class NamedTypeNodeTest extends TestCase
{
    #[Test]
    public function fromBuildsTheNameFromAString(): void
    {
        $node = NamedTypeNode::from('\Type\Name', 7);

        self::assertSame('\Type\Name', $node->name->toString());
        self::assertSame(7, $node->offset);
        self::assertSame(7, $node->name->offset);
        self::assertNull($node->arguments);
        self::assertNull($node->fields);
    }

    #[Test]
    public function fromKeepsAnExistingName(): void
    {
        $name = TypeName::from('Example');

        self::assertSame($name, NamedTypeNode::from($name)->name);
    }

    #[Test]
    public function fromRejectsAnEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NamedTypeNode::from('');
    }
}
