<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\Splitter;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;
use TypeLang\PhpDoc\Internal\Splitter\StringSplitter;

#[Revs(50), Warmup(5), Iterations(25), BeforeMethods('prepare'), RetryThreshold(2)]
final class StringSplitterBench extends SplitterBench
{
    protected SplitterInterface $splitter;

    public function prepare(): void
    {
        $this->splitter = new StringSplitter();
    }
}
