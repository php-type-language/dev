<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Parser\Grammar;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Description\TaggedDescription;
use TypeLang\PhpDoc\DocBlock\Grammar\UriGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\LinkTag\LinkTag;
use TypeLang\PhpDoc\DocBlock\Tag\LinkTag\LinkTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\Exception\MalformedTagException;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Grammar\Grammar;
use TypeLang\PhpDoc\Parser\Tag\StringTagParser;
use TypeLang\PhpDoc\Tests\TestCase;

final class DefinitionTest extends TestCase
{
    #[Test]
    public function ruleStringifiesToItsGrammar(): void
    {
        self::assertSame('<URI> [ <description> ]', (string) new LinkTagDefinition()->rule);
    }

    #[Test]
    public function matchesUriAndDescription(): void
    {
        $tag = self::factory()->create(
            'link',
            'https://example.com Some description',
            self::descriptions(),
        );

        self::assertInstanceOf(LinkTag::class, $tag);
        self::assertSame('link', $tag->name);
        self::assertSame('https://example.com', (string) $tag->uri);
        self::assertInstanceOf(DescriptionInterface::class, $tag->description);
        self::assertSame('Some description', (string) $tag->description);
    }

    #[Test]
    public function matchesUriWithoutDescription(): void
    {
        $tag = self::factory()->create('link', 'https://example.com', self::descriptions());

        self::assertInstanceOf(LinkTag::class, $tag);
        self::assertSame('https://example.com', (string) $tag->uri);
        self::assertNull($tag->description);
    }

    /**
     * The description delegate resolves nested inline tags, so a `{@...}` in the
     * description becomes a {@see TaggedDescription}.
     */
    #[Test]
    public function descriptionKeepsInlineTags(): void
    {
        $tag = self::factory()->create(
            'link',
            'https://example.com see {@link X}',
            self::descriptions(),
        );

        self::assertInstanceOf(LinkTag::class, $tag);
        self::assertInstanceOf(TaggedDescription::class, $tag->description);
        self::assertSame('see {@link X}', (string) $tag->description);
    }

    #[Test]
    public function missingRequiredUriIsMalformed(): void
    {
        $this->expectException(MalformedTagException::class);
        $this->expectExceptionMessage('Malformed "@link" tag, expected: <URI> [ <description> ]');

        self::factory()->create('link', '', self::descriptions());
    }

    /**
     * The reported offset points at where the tag body failed to match.
     */
    #[Test]
    public function malformedTagReportsFailureOffset(): void
    {
        try {
            self::factory()->create('link', '     ', self::descriptions());
            self::fail('Expected a MalformedTagException');
        } catch (MalformedTagException $e) {
            self::assertSame(5, $e->offset);
            self::assertSame('     ', $e->source);
        }
    }

    /**
     * A tag with no registered definition falls back to a plain tag whose whole
     * suffix becomes the description.
     */
    #[Test]
    public function unregisteredTagFallsBackToPlainTag(): void
    {
        $tag = self::factory()->create('unknown', 'free text', self::descriptions());

        self::assertNotInstanceOf(LinkTag::class, $tag);
        self::assertSame('unknown', $tag->name);
        self::assertSame('free text', (string) $tag->description);
    }

    /**
     * A suffix with no URI produces a malformed tag rather than an error.
     */
    #[Test]
    public function uriReaderSoftFailurePropagatesAsMalformedTag(): void
    {
        $this->expectException(MalformedTagException::class);

        self::factory()->create('link', "\t", self::descriptions());
    }

    private static function factory(): TagFactory
    {
        return new TagFactory(
            definitions: ['link' => new LinkTagDefinition()],
            grammar: self::grammar(),
        );
    }

    /**
     * A grammar whose only terminal, `URI`, reads a single whitespace-delimited
     * word and rejects an empty one.
     */
    private static function grammar(): Grammar
    {
        $grammar = new Grammar();

        $grammar->add('URI', new UriGrammarRule());

        return $grammar;
    }

    private static function descriptions(): DescriptionParserInterface
    {
        return new BalancedBraceAwareParser(new StringTagParser(new TagFactory()));
    }
}
