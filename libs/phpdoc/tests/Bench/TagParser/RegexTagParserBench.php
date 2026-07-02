<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\TagParser;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\Parser\Tag\RegexTagParser;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;

#[Revs(20), Warmup(5), Iterations(15), BeforeMethods('prepare'), RetryThreshold(2)]
final class RegexTagParserBench extends TagParserBench
{
    protected TagParserInterface $parser;

    #[\Override]
    public function prepare(): void
    {
        $this->parser = new RegexTagParser(new TagFactory());

        parent::prepare();
    }
}
