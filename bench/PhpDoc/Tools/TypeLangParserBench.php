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

#[Groups(['typelang', 'baseline']), Revs(500), Warmup(50), Iterations(15), BeforeMethods('prepare'), RetryThreshold(2)]
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
        $this->parser->parse($params['docblock']);
    }
}
