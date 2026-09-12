<?php

declare(strict_types=1);

namespace TypeLang\Bench\Parser\StringDecoding;

use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use PHPStan\PhpDocParser\Parser\StringUnescaper;

#[Groups(['phpstan']), Revs(1000), Warmup(50), Iterations(15), RetryThreshold(2)]
final class PHPStanStringUnescaperBench extends StringDecodingBench
{
    #[ParamProviders('stringsDataProvider')]
    public function benchDecodeString(array $params): void
    {
        StringUnescaper::unescapeString($params['string']);
    }
}
