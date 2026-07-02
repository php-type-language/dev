<?php

declare(strict_types=1);

namespace Bench\DescriptionParser;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Tag\RegexTagParser;
use TypeLang\PhpDoc\Tests\Bench\DescriptionParser\DescriptionParserBench;

#[Revs(20), Warmup(5), Iterations(15), BeforeMethods('prepare'), RetryThreshold(2)]
final class BalancedBraceAwareParserBench extends DescriptionParserBench
{
    protected DescriptionParserInterface $parser;

    #[\Override]
    public function prepare(): void
    {
        $this->parser = new BalancedBraceAwareParser(new RegexTagParser(new TagFactory()));

        parent::prepare();
    }
}
