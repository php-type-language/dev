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

#[Groups(['phpstan']), Revs(500), Warmup(50), Iterations(15), BeforeMethods('prepare'), RetryThreshold(2)]
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
        $iterator = new TokenIterator($this->lexer->tokenize($params['docblock']));

        $this->parser->parse($iterator);
    }
}
