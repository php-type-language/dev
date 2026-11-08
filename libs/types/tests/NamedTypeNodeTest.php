<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;

final class NamedTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorKeepsTheNameAndTheOffset(): void
    {
        $node = new NamedTypeNode(new Name('Name', new NamespaceName('Type', NamespaceKind::FullyQualified), 7), offset: 7);

        self::assertSame('\Type\Name', $node->name->toString());
        self::assertSame(7, $node->offset);
        self::assertSame(7, $node->name->offset);
        self::assertNull($node->arguments);
        self::assertNull($node->fields);
    }

    #[Test]
    public function emptyNameIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // @phpstan-ignore-next-line : An empty string is what the test passes on purpose
        new NamedTypeNode(new Name(''));
    }
}
