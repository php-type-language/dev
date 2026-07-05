<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Template\TemplateArgumentListNode;

final class NamedTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $name = Name::createFromString('int');
        $node = new NamedTypeNode($name);

        self::assertSame($name, $node->name);
    }

    #[Test]
    public function templateArgumentsDefaultToNull(): void
    {
        $node = new NamedTypeNode(Name::createFromString('array'));

        self::assertNull($node->arguments);
    }

    #[Test]
    public function fieldsDefaultToNull(): void
    {
        $node = new NamedTypeNode(Name::createFromString('array'));

        self::assertNull($node->fields);
    }

    #[Test]
    public function constructorStoresTemplateArguments(): void
    {
        $args = new TemplateArgumentListNode();
        $node = new NamedTypeNode(Name::createFromString('array'), $args);

        self::assertSame($args, $node->arguments);
    }

    #[Test]
    public function constructorStoresFields(): void
    {
        $fields = new FieldsListNode();
        $node = new NamedTypeNode(Name::createFromString('array'), null, $fields);

        self::assertSame($fields, $node->fields);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NamedTypeNode(Name::createFromString('string'));

        self::assertSame(0, $node->offset);
    }
}
