<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\Tests\TestCase;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\GenericTag;
use TypeLang\PhpDoc\DocBlock\Tag\Tag;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;

final class GenericTagTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $this->assertSame('param', new GenericTag('param')->name);
    }

    #[Test]
    public function descriptionDefaultsToNull(): void
    {
        $this->assertNull(new GenericTag('deprecated')->description);
    }

    #[Test]
    public function constructorConvertsStringDescription(): void
    {
        $tag = new GenericTag('param', 'int $a Some value');

        $this->assertInstanceOf(DescriptionInterface::class, $tag->description);
        $this->assertSame('int $a Some value', (string) $tag->description);
    }

    #[Test]
    public function isATag(): void
    {
        $tag = new GenericTag('param');

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertInstanceOf(TagInterface::class, $tag);
    }

    #[Test]
    public function toStringWithDescription(): void
    {
        $this->assertSame('@param int $a Some value', (string) new GenericTag('param', 'int $a Some value'));
    }

    #[Test]
    public function toStringWithoutDescription(): void
    {
        $this->assertSame('@deprecated', (string) new GenericTag('deprecated'));
    }
}
