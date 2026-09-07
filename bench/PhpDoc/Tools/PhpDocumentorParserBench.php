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

#[Groups(['phpdocumentor']), Revs(500), Warmup(50), Iterations(15), BeforeMethods('prepare'), RetryThreshold(2)]
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
        $this->parser->create($params['docblock']);
    }
}
