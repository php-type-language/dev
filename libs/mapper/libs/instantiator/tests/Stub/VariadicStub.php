<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests\Stub;

final class VariadicStub
{
    /**
     * @var array<array-key, string>
     */
    public readonly array $tags;

    public function __construct(
        public readonly int $count,
        string ...$tags,
    ) {
        $this->tags = $tags;
    }
}
