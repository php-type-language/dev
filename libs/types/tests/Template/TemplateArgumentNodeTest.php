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
    public function varianceDefaultsToNull(): void
    {
        $node = new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('int')));

        self::assertNull($node->variance);
    }

    #[Test]
    public function constructorAcceptsIdentifierVariance(): void
    {
        $variance = new Identifier('covariant');
        $node = new TemplateArgumentNode(
            new NamedTypeNode(Name::createFromString('T')),
            $variance,
        );

        self::assertSame($variance, $node->variance);
    }

    #[Test]
    public function constructorAcceptsNullVariance(): void
    {
        $node = new TemplateArgumentNode(
            new NamedTypeNode(Name::createFromString('T')),
            null,
        );

        self::assertNull($node->variance);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('T')));

        self::assertSame(0, $node->offset);
    }
}
