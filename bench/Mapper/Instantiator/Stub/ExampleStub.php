<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Instantiator\Stub;

final class ExampleStub
{
    public string $currency = 'USD';

    public ?string $comment = null;

    /**
     * @var list<string>
     */
    public array $tags = [];

    public function __construct(
        public int $amount = 0,
        public int $scale = 2,
    ) {}

    public static function create(int $amount, int $scale): self
    {
        return new self($amount, $scale);
    }
}
