<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Combinator\UrlCombinator;
use TypeLang\PhpDoc\DocBlock\Tag\LicenseTag\LicenseTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\LicenseTag\LicenseTag;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\TagFactory;
use TypeLang\PhpDoc\Tests\TestCase;

final class LicenseTagTest extends TestCase
{
    #[Test]
    public function parsesUrlForm(): void
    {
        $tag = self::factory()->create('license', 'https://opensource.org/licenses/MIT MIT License');

        self::assertInstanceOf(LicenseTag::class, $tag);
        self::assertSame('license', $tag->name);
        self::assertSame('https://opensource.org/licenses/MIT', (string) $tag->url);
        self::assertSame('MIT License', (string) $tag->description);
        self::assertSame('@license https://opensource.org/licenses/MIT MIT License', (string) $tag);
    }

    #[Test]
    public function resolvesThroughTheRealParser(): void
    {
        $block = new DocBlockParser()->parse('/** @license MIT */');

        self::assertCount(1, $block->tags);
        self::assertInstanceOf(LicenseTag::class, $block->tags[0]);
        self::assertNull($block->tags[0]->url);
        self::assertSame('MIT', (string) $block->tags[0]->description);
    }

    private static function factory(): TagFactory
    {
        return new TagFactory(
            definitions: [
                LicenseTagDefinition::NAME => new LicenseTagDefinition(),
            ],
            combinators: [
                UrlCombinator::NAME => new UrlCombinator(),
                DescriptionCombinator::NAME => new DescriptionCombinator(self::createDescriptionParser()),
            ],
        );
    }
}
