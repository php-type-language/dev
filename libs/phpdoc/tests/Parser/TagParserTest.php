<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Parser;

use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\Parser\Tag\RegexTagParser;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;
use TypeLang\PhpDoc\Tests\TestCase;

final class TagParserTest extends TestCase
{
    /**
     * @return iterable<string, array{TagParserInterface}>
     */
    public static function parserDataProvider(): iterable
    {
        yield 'RegexTagParser' => [
            new RegexTagParser(new TagFactory()),
        ];
    }
}
