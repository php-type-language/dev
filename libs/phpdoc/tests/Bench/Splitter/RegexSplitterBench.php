<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\Splitter;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\PhpDoc\Internal\Splitter\RegexSplitter;
use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;

#[Revs(50), Warmup(5), Iterations(20), BeforeMethods('prepare')]
final class RegexSplitterBench extends SplitterBench
{
    protected SplitterInterface $splitter;

    public function prepare(): void
    {
        $this->splitter = new RegexSplitter();
    }
}
