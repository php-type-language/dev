<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\DocBlock\Tests\TestCase;
use TypeLang\PhpDoc\DocBlock\Description\Description;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Tag;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;

/**
 * Tests the behaviour shared by every tag through the abstract {@see Tag} base
 * class, exercised here via a minimal concrete subclass.
 */
final class TagTest extends TestCase
{
    private function createTag(string $name, \Stringable|string|null $description = null): Tag
    {
        return new class($name, $description) extends Tag {};
    }

    #[Test]
    public function constructorStoresName(): void
    {
        $this->assertSame('param', $this->createTag('param')->name);
    }

    #[Test]
    public function descriptionDefaultsToNull(): void
    {
        $this->assertNull($this->createTag('param')->description);
    }

    #[Test]
    public function constructorConvertsStringDescription(): void
    {
        $tag = $this->createTag('param', 'int $a');

        $this->assertInstanceOf(DescriptionInterface::class, $tag->description);
        $this->assertSame('int $a', (string) $tag->description);
    }

    #[Test]
    public function constructorKeepsExistingDescriptionInstance(): void
    {
        $description = new Description('int $a');

        $this->assertSame($description, $this->createTag('param', $description)->description);
    }

    #[Test]
    public function implementsTagInterface(): void
    {
        $this->assertInstanceOf(TagInterface::class, $this->createTag('param'));
    }

    #[Test]
    public function toStringWithoutDescriptionRendersNameOnly(): void
    {
        $this->assertSame('@param', (string) $this->createTag('param'));
    }

    #[Test]
    public function toStringWithDescriptionRendersNameAndDescription(): void
    {
        $this->assertSame('@param int $a', (string) $this->createTag('param', 'int $a'));
    }

    #[Test]
    public function toStringTrimsTrailingSpaceForEmptyDescription(): void
    {
        $this->assertSame('@param', (string) $this->createTag('param', ''));
    }
}
