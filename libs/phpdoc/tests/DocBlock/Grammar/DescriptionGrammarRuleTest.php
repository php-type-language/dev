<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Grammar;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\DocBlock\Description\Description;
use TypeLang\PhpDoc\DocBlock\Description\TaggedDescription;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Tag\StringTagParser;

final class DescriptionGrammarRuleTest extends GrammarRuleTestCase
{
    protected function rule(): DescriptionGrammarRule
    {
        return new \ReflectionClass(DescriptionGrammarRule::class)
            ->newLazyProxy(function (DescriptionGrammarRule $proxy) {
                return new DescriptionGrammarRule(
                    descriptionParser: new BalancedBraceAwareParser(
                        tagParser: new StringTagParser(
                            tagFactory: new TagFactory(rules: [
                                DescriptionGrammarRule::NAME => $proxy,
                            ]),
                        ),
                    ),
                );
            });
    }

    #[Test]
    public function readsThePlainTrailingText(): void
    {
        $description = $this->matchText('Some description text');

        self::assertInstanceOf(Description::class, $description);
        self::assertSame('Some description text', (string) $description);
    }

    /**
     * The description delegate resolves inline tags, so a `{@...}` becomes a
     * {@see TaggedDescription}.
     */
    #[Test]
    public function keepsInlineTags(): void
    {
        $description = $this->matchText('see {@link X}');

        self::assertInstanceOf(TaggedDescription::class, $description);
        self::assertSame('see {@link X}', (string) $description);
    }

    /**
     * The whole remainder is consumed regardless of the whitespace it contains.
     */
    #[Test]
    public function consumesTheEntireRemainder(): void
    {
        $cursor = new Cursor('a b c');
        $description = $this->matchCursor($cursor);

        self::assertInstanceOf(Description::class, $description);
        self::assertSame('a b c', (string) $description);
        self::assertTrue($cursor->isEof);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function emptyDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'whitespace only' => ['   '];
    }

    #[Test]
    #[DataProvider('emptyDataProvider')]
    public function rejectsAnEmptyDescription(string $input): void
    {
        $this->expectException(NoMatchException::class);

        $this->matchText($input);
    }
}
