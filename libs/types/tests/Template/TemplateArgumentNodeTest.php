<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Tests\TestCase;

final class TemplateArgumentNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresValue(): void
    {
        $value = new NamedTypeNode(Name::createFromString('string'));
        $node = new TemplateArgumentNode($value);

        self::assertSame($value, $node->value);
    }

    #[Test]
    public function hintDefaultsToNull(): void
    {
        $node = new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertNull($node->hint);
    }

    #[Test]
    public function attributesDefaultToNull(): void
    {
        $node = new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertNull($node->attributes);
    }

    #[Test]
    public function constructorAcceptsIdentifierHint(): void
    {
        $hint = new Identifier('covariant');
        $node = new TemplateArgumentNode(
            new NamedTypeNode(Name::createFromString('T')),
            $hint,
        );

        self::assertSame($hint, $node->hint);
    }

    #[Test]
    public function constructorAcceptsNullHint(): void
    {
        $node = new TemplateArgumentNode(
            new NamedTypeNode(Name::createFromString('T')),
            null,
        );

        self::assertNull($node->hint);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('T')));

        self::assertSame(0, $node->offset);
    }
}
