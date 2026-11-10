<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests\Stub;

final class TraitConsumerStub
{
    use ExampleTrait;

    public string $ownProperty = 'own-value';
}
