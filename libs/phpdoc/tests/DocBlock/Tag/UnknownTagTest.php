<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\DocBlock\Tests\TestCase;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\InvalidTagInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Tag;
use TypeLang\PhpDoc\DocBlock\Tag\UnknownTag;

final class UnknownTagTest extends TestCase
{
    #[Test]
    public function nameIsTheDefaultUnknownName(): void
    {
        $tag = new UnknownTag(new \RuntimeException());

        $this->assertSame(UnknownTag::DEFAULT_UNKNOWN_TAG_NAME, $tag->name);
    }

    #[Test]
    public function defaultUnknownNameConstantValue(): void
    {
        $this->assertSame('unknown', UnknownTag::DEFAULT_UNKNOWN_TAG_NAME);
    }

    #[Test]
    public function constructorStoresReason(): void
    {
        $reason = new \RuntimeException('unrecognized');
        $tag = new UnknownTag($reason);

        $this->assertSame($reason, $tag->reason);
    }

    #[Test]
    public function descriptionDefaultsToNull(): void
    {
        $this->assertNull(new UnknownTag(new \RuntimeException())->description);
    }

    #[Test]
    public function constructorConvertsStringDescription(): void
    {
        $tag = new UnknownTag(new \RuntimeException(), '@foo bar');

        $this->assertInstanceOf(DescriptionInterface::class, $tag->description);
        $this->assertSame('@foo bar', (string) $tag->description);
    }

    #[Test]
    public function isAnInvalidTag(): void
    {
        $tag = new UnknownTag(new \RuntimeException());

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertInstanceOf(InvalidTagInterface::class, $tag);
    }

    /**
     * Characterization test: documents the current string representation, which
     * prefixes the stored description with an "@" symbol regardless of its
     * content (see the inconsistency note in the change summary).
     */
    #[Test]
    public function toStringPrefixesDescriptionWithAtSign(): void
    {
        $this->assertSame('@plain', (string) new UnknownTag(new \RuntimeException(), 'plain'));
    }

    /**
     * Characterization test: when the description already begins with "@" (as
     * produced by the phpdoc parser, which passes the full raw tag source), the
     * current implementation yields a doubled "@@" prefix.
     */
    #[Test]
    public function toStringDoublesAtSignWhenDescriptionAlreadyStartsWithIt(): void
    {
        $this->assertSame('@@foo bar', (string) new UnknownTag(new \RuntimeException(), '@foo bar'));
    }

    /**
     * Characterization test: a missing description currently renders as a bare
     * "@" symbol.
     */
    #[Test]
    public function toStringWithoutDescriptionRendersBareAtSign(): void
    {
        $this->assertSame('@', (string) new UnknownTag(new \RuntimeException()));
    }
}
