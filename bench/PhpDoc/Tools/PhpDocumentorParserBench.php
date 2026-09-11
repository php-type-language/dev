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
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

#[Groups(['phpdocumentor']), Revs(1), Warmup(1), Iterations(10)]
#[BeforeMethods('prepare'), RetryThreshold(5)]
final class PhpDocumentorParserBench extends DocBlockParserBench
{
    private DocBlockFactoryInterface $parser;

    public function prepare(): void
    {
        $this->parser = DocBlockFactory::createInstance();
    }

    #[ParamProviders('docBlocksDataProvider')]
    public function benchParseDocBlock(array $params): void
    {
        foreach ($params['docblocks'] as $docblock) {
            try {
                $this->parser->create($docblock);
            } catch (\Throwable) {
                // A real-world corpus contains DocBlocks that some of the tools
                // are not able to parse. They are skipped so that every tool
                // is measured on the same corpus.
            }
        }
    }
}
