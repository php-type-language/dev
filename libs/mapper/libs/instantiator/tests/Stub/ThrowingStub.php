<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests\Stub;

final class ThrowingStub
{
    public function __construct()
    {
        throw new \DomainException('Construction has failed');
    }
}
