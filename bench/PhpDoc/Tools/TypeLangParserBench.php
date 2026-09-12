<?php

declare(strict_types=1);

namespace TypeLang\Bench\PhpDoc\Tools;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\DocBlockParserInterface;

#[Groups(['typelang', 'baseline']), Revs(3), Warmup(10), Iterations(5)]
#[BeforeMethods('prepare'), RetryThreshold(5)]
final class TypeLangParserBench extends DocBlockParserBench
{
    private DocBlockParserInterface $parser;

    public function prepare(): void
    {
        $this->parser = new DocBlockParser();
    }

    #[ParamProviders('docBlocksDataProvider')]
    public function benchParseDocBlock(array $params): void
    {
        foreach ($params['docblocks'] as $docblock) {
            try {
                $this->parser->parse($docblock);
            } catch (\Throwable) {
                // A real-world corpus contains DocBlocks that some of the tools
                // are not able to parse. They are skipped so that every tool
                // is measured on the same corpus.
            }
        }
    }
}
