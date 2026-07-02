<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\DescriptionParser;

use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

abstract class DescriptionParserBench
{
    private const string DESCRIPTION_SAMPLE = <<<'DOC'
        Cross product of every parser with line-ending fixtures. Each fixture
        declares the EXACT segments (verbatim text including the trailing line
        terminator, plus its byte offset) the parser {@see example} is expected
        to produce.

        @return iterable<string, array{CommentParserInterface, string, list<array{string, int<0, max>}>}>
        DOC;

    private string $littleDescription;
    private string $bigDescription;

    abstract protected DescriptionParserInterface $parser {
        get;
    }

    public function prepare(): void
    {
        $this->littleDescription = self::DESCRIPTION_SAMPLE;
        $this->bigDescription = \str_repeat(self::DESCRIPTION_SAMPLE, 30);
    }

    public function benchParseLittleDescription(): void
    {
        $this->parser->parse($this->littleDescription);
    }

    public function benchParseBigDescription(): void
    {
        $this->parser->parse($this->bigDescription);
    }
}
