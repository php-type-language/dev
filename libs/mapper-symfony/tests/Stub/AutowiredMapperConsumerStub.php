<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TypeLang\Mapper\DenormalizerInterface;

final class AutowiredMapperConsumerStub
{
    public function __construct(
        #[Autowire(service: 'type_lang_mapper.mapper.api')]
        public readonly DenormalizerInterface $mapper,
    ) {}
}
