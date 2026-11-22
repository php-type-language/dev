<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperFormatter;
use TypeLang\Mapper\Diagnostics\Violation;
use TypeLang\Mapper\Diagnostics\ViolationFormatterInterface;

#[AsMapperFormatter('denormalization', mappers: ['api'])]
final class ShoutingFormatterStub implements ViolationFormatterInterface
{
    public function format(Violation $violation): string
    {
        return 'INVALID ' . \strtoupper($violation->code->getCode());
    }
}
