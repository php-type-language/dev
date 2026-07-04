<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\DocBlock\Grammar\AuthorNameGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\EmailGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\AuthorTag\AuthorTag;
use TypeLang\PhpDoc\DocBlock\Tag\AuthorTag\AuthorTagDefinition;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\TagFactory;
use TypeLang\PhpDoc\Tests\TestCase;

final class AuthorTagTest extends TestCase
{
    #[Test]
    public function parsesNameAndEmail(): void
    {
        $tag = self::factory()->create('author', 'John Doe <john@example.com>');

        self::assertInstanceOf(AuthorTag::class, $tag);
        self::assertSame('author', $tag->name);
        self::assertSame('John Doe', $tag->author);
        self::assertSame('john@example.com', $tag->email);
        self::assertSame('@author John Doe <john@example.com>', (string) $tag);
    }

    #[Test]
    public function parsesNameWithoutEmail(): void
    {
        $tag = self::factory()->create('author', 'Jane Roe');

        self::assertInstanceOf(AuthorTag::class, $tag);
        self::assertSame('Jane Roe', $tag->author);
        self::assertNull($tag->email);
        self::assertSame('@author Jane Roe', (string) $tag);
    }

    #[Test]
    public function resolvesThroughTheRealParser(): void
    {
        $block = new DocBlockParser()->parse('/** @author Kirill <k@example.com> */');

        self::assertCount(1, $block->tags);
        self::assertInstanceOf(AuthorTag::class, $block->tags[0]);
        self::assertSame('k@example.com', $block->tags[0]->email);
    }

    private static function factory(): TagFactory
    {
        return new TagFactory(
            definitions: [
                AuthorTagDefinition::NAME => new AuthorTagDefinition(),
            ],
            rules: [
                AuthorNameGrammarRule::NAME => new AuthorNameGrammarRule(),
                EmailGrammarRule::NAME => new EmailGrammarRule(),
            ],
        );
    }
}
