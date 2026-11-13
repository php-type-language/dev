<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests\Stub;

class ParentStub
{
    final public function __construct(
        public readonly int $value,
    ) {}

    public static function create(int $value): static
    {
        return new static($value);
    }
}
