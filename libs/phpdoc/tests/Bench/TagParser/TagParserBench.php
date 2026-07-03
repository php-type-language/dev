<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\TagParser;

use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;

abstract class TagParserBench
{
    private const string TAG_SAMPLE = <<<'DOC'
        @return iterable<string, array{CommentParserInterface, string, list<array{string, int<0, max>}>}>
        DOC;

    private string $tag;

    abstract protected TagParserInterface $parser {
        get;
    }

    private DescriptionParserInterface $descriptions;

    public function prepare(): void
    {
        $this->tag = self::TAG_SAMPLE;

        $this->descriptions = new BalancedBraceAwareParser(
            tagParser: $this->parser,
        );
    }

    public function benchParseTag(): void
    {
        $this->parser->parse($this->tag, $this->descriptions);
    }
}
