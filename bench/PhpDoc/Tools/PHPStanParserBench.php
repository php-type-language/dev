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
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;

#[Groups(['phpstan']), Revs(3), Warmup(10), Iterations(5)]
#[BeforeMethods('prepare'), RetryThreshold(5)]
final class PHPStanParserBench extends DocBlockParserBench
{
    private Lexer $lexer;
    private PhpDocParser $parser;

    public function prepare(): void
    {
        $config = new ParserConfig(usedAttributes: [
            'lines' => true,
            'indexes' => true,
            'comments' => true,
        ]);

        $this->lexer = new Lexer($config);

        $constExprParser = new ConstExprParser($config);
        $typeParser = new TypeParser($config, $constExprParser);
        $this->parser = new PhpDocParser($config, $typeParser, $constExprParser);
    }

    #[ParamProviders('docBlocksDataProvider')]
    public function benchParseDocBlock(array $params): void
    {
        foreach ($params['docblocks'] as $docblock) {
            try {
                $this->parser->parse(new TokenIterator($this->lexer->tokenize($docblock)));
            } catch (\Throwable) {
                // A real-world corpus contains DocBlocks that some of the tools
                // are not able to parse. They are skipped so that every tool
                // is measured on the same corpus.
            }
        }
    }
}
