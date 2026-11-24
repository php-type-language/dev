<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use TypeLang\Mapper\Mapping\SkipWhen;

final class BlankSkipStub
{
    #[SkipWhen('blank(value)')]
    public string $email = '';

    public string $name = 'John';
}
