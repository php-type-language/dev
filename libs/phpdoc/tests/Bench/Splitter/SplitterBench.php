<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\Splitter;

use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;

abstract class SplitterBench
{
    protected const string SAMPLE = <<<'DOC'
        /**
         * Cross product of every parser with line-ending fixtures. Each fixture
         * declares the EXACT segments (verbatim text including the trailing line
         * terminator, plus its byte offset) the parser is expected to produce.
         *
         * @return iterable<string, array{CommentParserInterface, string, list<array{string, int<0, max>}>}>
         */
        DOC;

    abstract protected SplitterInterface $splitter {
        get;
    }

    public function benchSplit(): void
    {
        foreach ($this->splitter->split(self::SAMPLE) as $segment) {
            // NO-OP
        }
    }
}
