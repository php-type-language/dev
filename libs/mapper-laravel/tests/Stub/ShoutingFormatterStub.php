<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests\Stub;

use TypeLang\Mapper\Diagnostics\Violation;
use TypeLang\Mapper\Diagnostics\ViolationFormatterInterface;

final class ShoutingFormatterStub implements ViolationFormatterInterface
{
    public function format(Violation $violation): string
    {
        return 'INVALID ' . \strtoupper($violation->code->getCode());
    }
}
