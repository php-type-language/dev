<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use TypeLang\Mapper\DenormalizerInterface;

/**
 * Service whose argument is named after a named mapper, without saying which
 * mapper it takes.
 */
final class NamedArgumentConsumerStub
{
    public function __construct(
        public readonly DenormalizerInterface $apiMapper,
    ) {}
}
