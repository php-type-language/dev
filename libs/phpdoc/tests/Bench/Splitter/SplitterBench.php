<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\Splitter;

use TypeLang\PhpDoc\Parser\Splitter\SplitterInterface;

abstract class SplitterBench
{
    private const string DOC_BLOCK_SAMPLE = <<<'DOC'
         * Cross product of every parser with line-ending fixtures. Each fixture
         * declares the EXACT segments (verbatim text including the trailing line
         * terminator, plus its byte offset) the parser is expected to produce.
         *
         * @return iterable<string, array{CommentParserInterface, string, list<array{string, int<0, max>}>}>
        DOC;

    private const string DOC_BLOCK_TEMPLATE = "/**\n%s\n*/";

    private string $littleDocBlock;
    private string $bigDocBlock;

    abstract protected SplitterInterface $splitter {
        get;
    }

    public function prepare(): void
    {
        $this->littleDocBlock = \vsprintf(self::DOC_BLOCK_TEMPLATE, [
            self::DOC_BLOCK_SAMPLE,
        ]);

        $this->bigDocBlock = \vsprintf(self::DOC_BLOCK_TEMPLATE, [
            \str_repeat(self::DOC_BLOCK_SAMPLE, 30),
        ]);
    }

    public function benchSplitLittleDocBlock(): void
    {
        foreach ($this->splitter->split($this->littleDocBlock) as $segment) {
            // NO-OP
        }
    }

    public function benchSplitBigDocBlock(): void
    {
        foreach ($this->splitter->split($this->bigDocBlock) as $segment) {
            // NO-OP
        }
    }
}
