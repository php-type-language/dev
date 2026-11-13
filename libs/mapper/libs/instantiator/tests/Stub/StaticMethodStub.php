<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests\Stub;

final class StaticMethodStub
{
    public function __construct(
        public readonly int $value = 0,
    ) {}

    public static function create(int $value): self
    {
        return new self($value);
    }

    public static function createFailing(): self
    {
        throw new \DomainException('Factory has failed');
    }

    public static function createForeign(): object
    {
        return new \stdClass();
    }

    public static function createScalar(): int
    {
        return 42;
    }

    public function createInstance(): self
    {
        return new self();
    }

    protected static function createProtected(): self
    {
        return new self();
    }
}
