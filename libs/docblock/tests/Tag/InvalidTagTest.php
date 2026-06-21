<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Tests\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\DocBlock\Description\DescriptionInterface;
use TypeLang\DocBlock\Tag\InvalidTag;
use TypeLang\DocBlock\Tag\InvalidTagInterface;
use TypeLang\DocBlock\Tag\Tag;
use TypeLang\DocBlock\Tests\TestCase;

final class InvalidTagTest extends TestCase
{
    #[Test]
    public function constructorStoresName(): void
    {
        $tag = new InvalidTag('param', new \RuntimeException());

        $this->assertSame('param', $tag->name);
    }

    #[Test]
    public function constructorStoresReason(): void
    {
        $reason = new \RuntimeException('broken');
        $tag = new InvalidTag('param', $reason);

        $this->assertSame($reason, $tag->reason);
    }

    #[Test]
    public function descriptionDefaultsToNull(): void
    {
        $this->assertNull(new InvalidTag('param', new \RuntimeException())->description);
    }

    #[Test]
    public function constructorConvertsStringDescription(): void
    {
        $tag = new InvalidTag('param', new \RuntimeException(), 'int $a');

        $this->assertInstanceOf(DescriptionInterface::class, $tag->description);
        $this->assertSame('int $a', (string) $tag->description);
    }

    #[Test]
    public function isAnInvalidTag(): void
    {
        $tag = new InvalidTag('param', new \RuntimeException());

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertInstanceOf(InvalidTagInterface::class, $tag);
    }

    #[Test]
    public function toStringWithDescription(): void
    {
        $tag = new InvalidTag('param', new \RuntimeException(), 'int $a');

        $this->assertSame('@param int $a', (string) $tag);
    }

    #[Test]
    public function toStringWithoutDescription(): void
    {
        $tag = new InvalidTag('param', new \RuntimeException());

        $this->assertSame('@param', (string) $tag);
    }
}
